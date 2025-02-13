class View {
    formulario;
    constructor() {
    }
    inizialize(form) {
        this.formulario=form;
        console.log(this.formulario)
        const titulos = this.getTitles(this.formulario.titulo, this.formulario.descripcion);
        $("#contenedor").append(titulos);
        this.formulario.secciones.forEach(element => {
            let seccion = "";
            var pregunta = this.getPregunta(element);
            seccion += `
            <div class="col-md-10" data-seccion="${element.subtitulo}" data-id="${element.form_seccion_id}">
                <div id="titulo" class="ms-panel">
                    <div class="ms-panel-header">
                        <div class="row">
                            <div class="col-md-12">
                                <h6><small>${element.subtitulo}</small></h6>
                            </div>
                            <div class="col-md-12">
                                <p>${element.descripcion=='null' ? element.descripcion:''}</p>
                            </div>
                        </div>
                        <hr>
                        ${pregunta}
                    </div>
                </div>
            </div>
            `;
            $("#contenedor").append(seccion);
        });

    }
    getTitles(titulos, descripcion) {
        const res = `
        <div class="col-md-10">
            <div id="titulo" class="ms-panel">
                <div class="ms-panel-header ms-panel-custome">
                    <div class="row">
                        <div class="col-md-12">
                            <h6>${titulos}</h6>
                        </div>
                        <div class="col-md-12">
                            <p>${descripcion=='null' ? descripcion:''}</p>
                        </div>
                    </div>
                </div>
            
            </div>
        </div>
        `;
        return res;
    }
    getPregunta(secciones) {
        let respuesta_data="";
        var preguntas = secciones.preguntas.forEach(element => {
            if (element.tipo === "escala") {
                //console.log('es escalar');
                let escalar = this.getScale(element);
                respuesta_data +=escalar;
            }
            if (element.tipo === "check") {
                //console.log('es check');
                let check = this.getCheckBox(element);
                respuesta_data +=check;
            }
            if (element.tipo === "box") {
                //console.log('es box');
                let box = this.getBox(element);
                respuesta_data +=box;
            }
            if (element.tipo === "text") {
                //console.log('es text');
                let text = this.getParagraph(element);
                respuesta_data +=text;
            }
        });
        return respuesta_data;
    }
    getCheckBox(element) {
        var checkbox = '';
        element.respuestas.forEach(elem => {
            checkbox += `
                <li style="margin-bottom: 0.5rem;">
                    <label class="ms-checkbox-wrap ms-checkbox-primary">
                        <input type="checkbox" value="${elem.form_respuesta_id}" name="${elem.form_pregunta_id}[]">
                        <i class="ms-checkbox-check"></i>
                    </label>
                    <span style="font-size: 13px">${elem.val}</span>
                </li>
            `;
        });
        const res = `
                    <div class="row checkbox" data-id="${element.form_pregunta_id}">
                        <div class="col-md-12">
                            <p style="font-size: 14px">${element.pregunta}</p>
                            <br>
                        </div>
                        <div class="col-md-12">
                            <ul class="ms-list ms-list-display">
                               ${checkbox}
                            </ul>
                        </div>
                    </div>
        `;
        return res;
    }
    getBox(element) {
        var checkbox = '';
        element.respuestas.forEach(elem => {
            checkbox += `
                <li style="margin-bottom: 0.5rem;">
                    <label class="ms-checkbox-wrap ms-checkbox-primary">
                    <input type="radio" value="${elem.form_respuesta_id}" name="${element.form_pregunta_id}" required>
                    <i class="ms-checkbox-check"></i>
                    </label>
                    <span style="font-size: 13px">${elem.val} </span>
                </li>
            `;
        });
        const res = `
                    <div class="row box" data-id="${element.form_pregunta_id}">
                        <div class="col-md-12">
                            <p style="font-size: 14px">${element.pregunta}</p>
                            <br>
                        </div>
                        <div class="col-md-12">
                            <ul class="ms-list ms-list-display">
                               ${checkbox}
                            </ul>
                        </div>
                    </div>
        `;
        return res;
    }
    getParagraph(element) {
        const res = `
                    <div class="row text" data-id="${element.form_pregunta_id}">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="exampleTextarea"><p style="font-size: 14px">${element.pregunta}</p></label>
                                <textarea name="${element.respuestas[0].form_respuesta_id}"class="form-control" id="exampleTextarea" rows="3" required></textarea>
                            </div>
                        </div>
                    </div>
        `;
        return res;
    }
    getScale(element) {
        var campos="";
        element.respuestas.forEach(elem => {
            campos+=`
                <div class="col-md-1">
                    <label class="ms-checkbox-wrap ms-checkbox-primary">
                        <input type="radio" value="${elem.form_respuesta_id}" name="${element.form_pregunta_id}" required >
                        <i class="ms-checkbox-check"></i>
                    </label>
                    <span style="font-size: 13px" >${elem.valor}</span>
                </div>
            `;
        });
        const res = `
                    <div class="row scale" data-id="${element.form_pregunta_id}">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="exampleTextarea"><p style="font-size: 14px">${element.pregunta}</p></label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-1">
                                    <p>Poor</p>
                                </div>
                                ${campos}
                                <div class="col-md-1">
                                    <p>Excellent</p>
                                </div>
                            </div>
                        </div>
                    </div>
        `;
        return res;
    }
}