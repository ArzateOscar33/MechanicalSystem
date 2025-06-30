(() => {
    const { jsPDF } = window.jspdf;

     const btnGraficos = document.querySelector("#generarGraficosEspeciales");
    if (btnGraficos) {
        btnGraficos.addEventListener("click", mostrarFormularioGraficosEspeciales);
    }
 
    function mostrarFormularioGraficosEspeciales() {
        Swal.fire({
            title: "Generar Informe PDF",
            html: `
                <label>Selecciona el mes:</label>
                <input type="month" id="mesGrafico" class="swal2-input" max="${new Date().toISOString().slice(0, 7)}">
                <label>Fecha para gráfico por inspector:</label>
                <input type="date" id="fechaInspector" class="swal2-input" max="${new Date().toISOString().split('T')[0]}">
            `,
            confirmButtonText: "Generar PDF",
            preConfirm: () => {
                const mes = document.getElementById("mesGrafico").value;
                const fecha = document.getElementById("fechaInspector").value;
                if (!mes || !fecha) {
                    Swal.showValidationMessage("Ambos campos son obligatorios.");
                    return false;
                }
                return { mes, fecha };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                generarInforme(result.value);
            }
        });
    }

    async function generarInforme({ mes, fecha }) {
        const contenedor = document.createElement("div");
        contenedor.style.display = "block";
        contenedor.innerHTML = `
            <div id="header-grafico" style="margin-bottom: 20px;"></div>
            <div id="grid-ciudades" style="display: flex; flex-wrap: wrap; gap: 10px; height: 1600px;"></div>
            <div id="footer-grafico" style="margin-top: 20px;"></div>
        `;
        document.body.appendChild(contenedor);

        Swal.fire({
            title: 'Generando PDF...',
            html: 'Esto puede tardar unos segundos',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        const [anio, mesNum] = mes.split("-");

        await clonarCanvasEn("fechaChart", "Certificados por Mes", contenedor.querySelector("#header-grafico"), 1500, 250);

        const ciudades = await fetch(base_url + "admin/getCiudades")
            .then(res => res.json())
            .then(data => data.map(d => d.city));

        for (const ciudad of ciudades) {
            await generarGraficoCiudad(anio, mesNum, ciudad, contenedor.querySelector("#grid-ciudades"));
        }

        await generarGraficoInspectorPorFecha(fecha, contenedor.querySelector("#footer-grafico"), 1500, 400);

        const pdf = new jsPDF("p", "pt", "a4");
        const pageHeight = pdf.internal.pageSize.getHeight();
        let currentY = 20;

        const blocks = ["#header-grafico", "#grid-ciudades", "#footer-grafico"];

        for (const sel of blocks) {
            const div = contenedor.querySelector(sel);
            const canvas = await html2canvas(div, {
                useCORS: true,
                scale: 1,
                allowTaint: false
            });

            const imgData = canvas.toDataURL("image/png");
            const imgProps = pdf.getImageProperties(imgData);
            const pdfWidth = pdf.internal.pageSize.getWidth() - 40;
            const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

            if (currentY + pdfHeight > pageHeight - 20) {
                pdf.addPage();
                currentY = 20;
            }

            pdf.addImage(imgData, "PNG", 20, currentY, pdfWidth, pdfHeight);
            currentY += pdfHeight + 10;
        }

        document.body.removeChild(contenedor);
        Swal.close();
        pdf.save(`Informe_Graficos_${mes}_${fecha}.pdf`);
        Swal.fire("Informe generado", "El PDF fue descargado correctamente", "success");
    }

    async function clonarCanvasEn(id, titulo, destino, width = 794, height = 300) {
        const original = document.getElementById(id);
        if (!original) return;

        const div = document.createElement("div");
        div.style.padding = "10px";
        div.innerHTML = `<h5>${titulo}</h5>`;

        const canvas = document.createElement("canvas");
        canvas.width = width;
        canvas.height = height;
        const ctx = canvas.getContext("2d");
        ctx.drawImage(original, 0, 0, width, height);

        div.appendChild(canvas);
        destino.appendChild(div);
    }

    async function generarGraficoCiudad(anio, mes, ciudad, destino) {
      const data = await fetch(base_url + "admin/certificadosPorDia", {
        method: "POST",
        body: new URLSearchParams({ anio, mes, ciudad }),
      }).then((r) => r.json());

      const dias = data.map((d) => d.dia);
      const totales = data.map((d) => parseInt(d.total));

      const wrapper = document.createElement("div");
      wrapper.style.flex = "1 1 48%";
      wrapper.style.border = "1px solid #ccc";
      wrapper.style.padding = "10px";

      const title = document.createElement("h6");
      title.textContent = `Certificados por Día - ${ciudad}`;
      wrapper.appendChild(title);

      const canvas = document.createElement("canvas");
      canvas.width = 800;
      canvas.height = 400;
      wrapper.appendChild(canvas);
      destino.appendChild(wrapper);

      new Chart(canvas.getContext("2d"), {
        type: "line",
        data: {
          labels: dias,
          datasets: [
            {
              label: `Ciudad (${ciudad})`,
              data: totales,
              borderColor: "#17a2b8",
              backgroundColor: "rgba(23, 197, 17, 0.2)",
              fill: true,
              tension: 0.4,
              borderWidth: 1,
            },
          ],
        },

        options: {
          responsive: false,
          
 
          plugins: {
            datalabels: {
              anchor: "end",
              align: "top",
              color: "#000",
              font: {
                weight: "bold",
              },
              formatter: function (value) {
                return value;
              },
            },
    
            title: {
              display: true,
              text: "Distribución Diaria de Certificados",
            },
          },
        },
      });
    }

    async function generarGraficoInspectorPorFecha(fecha, destino, width = 794, height = 300) {
        const data = await fetch(base_url + "estadisticas/certificadosPorInspectorPorFecha", {
            method: "POST",
            body: new URLSearchParams({ fecha })
        }).then(r => r.json());

        const inspectores = data.map(d => d.inspector_name);
        const totales = data.map(d => parseInt(d.total));

        const div = document.createElement("div");
        div.style.padding = "10px";
        div.innerHTML = `<h5>Certificados por Inspector (${fecha})</h5>`;

        const canvas = document.createElement("canvas");
        canvas.width = width;
        canvas.height = height;
        div.appendChild(canvas);
        destino.appendChild(div);

        new Chart(canvas.getContext("2d"), {
            type: "bar",
            data: {
                labels: inspectores,
                datasets: [{
                    label: "Certificados",
                    data: totales,
                    backgroundColor: "rgba(54, 162, 235, 0.5)",
                    borderColor: "rgba(54, 162, 235, 1)",
                    borderWidth: 1
                }]
            },
            plugins: {
                datalabels: {
                    anchor: 'end',
                    align: 'top',
                    color: '#000',
                    font: {
                        weight: 'bold'
                    },
                    formatter: function (value) {
                        return value;
                    }
                },
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: `Certificados por Inspector (${fecha})`
                }
            },
            options: {
                responsive: false,
                maintainAspectRatio: false
            }
        });
    }
})();