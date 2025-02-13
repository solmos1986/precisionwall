class View {
    formulario;
    constructor() {
        if (save !== null) {
            var save = localStorage.getItem('form');
            save = JSON.parse(save);
            //set formulario
            this.formulario = save;
        } else {
            console.log('no data');
        }
    }
    inizialize() {
        //console.log(this.formulario)
        const titulos = this.getTitles(this.formulario.title, this.formulario.description);
        $("#contenedor").append(titulos);
        this.formulario.secciones.forEach(element => {
            let seccion = "";
            var pregunta = this.getPregunta(element);
            seccion += `
            <div class="col-md-10">
                <div id="titulo" class="ms-panel">
                    <div class="ms-panel-header ">
                        <div class="row">
                            <div class="col-md-12">
                                <h6><small>${element.subtitulo}</small></h6>
                            </div>
                            <div class="col-md-12">
                                <p>${element.descripcion}</p>
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
                            <p>${descripcion}</p>
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
                console.log('es escalar');
                let escalar = this.getScale(element);
                respuesta_data +=escalar;
            }
            if (element.tipo === "check") {
                console.log('es check');
                let check = this.getCheckBox(element);
                respuesta_data +=check;
            }
            if (element.tipo === "box") {
                console.log('es box');
                let box = this.getBox(element);
                respuesta_data +=box;
            }
            if (element.tipo === "text") {
                console.log('es text');
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
                        <input type="checkbox" ${elem.valor}>
                        <i class="ms-checkbox-check"></i>
                    </label>
                    <span style="font-size: 13px">${elem.val}</span>
                </li>
            `;
        });
        const res = `
                    <div class="row">
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
                    <input type="radio" value="${elem.valor}" name="radioExample2">
                    <i class="ms-checkbox-check"></i>
                    </label>
                    <span style="font-size: 13px">${elem.val} </span>
                </li>
            `;
        });
        const res = `
                    <div class="row">
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
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="exampleTextarea"><p style="font-size: 14px">${element.pregunta}</p></label>
                                <textarea class="form-control" id="exampleTextarea" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
        `;
        return res;
    }
    getScale(element) {
        const res = `
                    <div class="row">
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
                                <div class="col-md-1">
                                    <label class="ms-checkbox-wrap ms-checkbox-primary">
                                        <input type="radio" value="" name="radioExample2">
                                        <i class="ms-checkbox-check"></i>
                                    </label>
                                    <span style="font-size: 13px" >1</span>
                                </div>
                                <div class="col-md-1">
                                    <label class="ms-checkbox-wrap ms-checkbox-primary">
                                        <input type="radio" value="" name="radioExample2">
                                        <i class="ms-checkbox-check"></i>
                                    </label>
                                    <span style="font-size: 13px" >2</span>
                                </div>
                                <div class="col-md-1">
                                    <label class="ms-checkbox-wrap ms-checkbox-primary">
                                        <input type="radio" value="" name="radioExample2">
                                        <i class="ms-checkbox-check"></i>
                                    </label>
                                    <span style="font-size: 13px" >3</span>    
                                </div>
                                <div class="col-md-1">
                                    <label class="ms-checkbox-wrap ms-checkbox-primary">
                                        <input type="radio" value="" name="radioExample2">
                                        <i class="ms-checkbox-check"></i>
                                    </label>
                                    <span style="font-size: 13px" >4</span>
                                </div>
                                <div class="col-md-1">
                                    <label class="ms-checkbox-wrap ms-checkbox-primary">
                                        <input type="radio" value="" name="radioExample2">
                                        <i class="ms-checkbox-check"></i>
                                    </label>
                                    <span style="font-size: 13px" >5</span>
                                </div>
                                <div class="col-md-1">
                                    <label class="ms-checkbox-wrap ms-checkbox-primary">
                                        <input type="radio" value="" name="radioExample2">
                                        <i class="ms-checkbox-check"></i>
                                    </label>
                                    <span style="font-size: 13px" >6</span>
                                </div>
                                <div class="col-md-1">
                                    <label class="ms-checkbox-wrap ms-checkbox-primary">
                                        <input type="radio" value="" name="radioExample2">
                                        <i class="ms-checkbox-check"></i>
                                    </label>
                                    <span style="font-size: 13px" >7</span>
                                </div>
                                <div class="col-md-1">
                                    <label class="ms-checkbox-wrap ms-checkbox-primary">
                                        <input type="radio" value="" name="radioExample2">
                                        <i class="ms-checkbox-check"></i>
                                    </label>
                                    <span style="font-size: 13px">8</span>
                                </div>
                                <div class="col-md-1">
                                    <label class="ms-checkbox-wrap ms-checkbox-primary">
                                        <input type="radio" value="" name="radioExample2">
                                        <i class="ms-checkbox-check"></i>
                                    </label>
                                    <span style="font-size: 13px" >9</span>
                                </div>
                                <div class="col-md-1">
                                    <label class="ms-checkbox-wrap ms-checkbox-primary">
                                        <input type="radio" value="" name="radioExample2">
                                        <i class="ms-checkbox-check"></i>
                                    </label>
                                    <span style="font-size: 13px" >10</span>
                                </div>
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