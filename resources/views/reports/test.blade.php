<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>reporte de proyectos</title>
    <!-- Bootstrap core CSS -->

</head>
<style>
    table,
    th,
    td {
        overflow-x: auto;
        width: 100%;
        border: 1px solid black;

    }

    th,
    td {
        padding: 5px;
        text-align: center;

    }
</style>

<body>
    <div class="container">
        <h1>reporte de proyectos</h1>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <th scope="col">Id Empresa</th>
                    <th scope="col">Codigo</th>
                    <th scope="col">Nombre empresa</th>
                    <th scope="col">Id Proyecto</th>
                    <th scope="col">Nombre Proyecto</th>
                    <th scope="col">Tipo Proyecto</th>
                </thead>
                <tbody>
                    @foreach($proyectos as $proyecto)
                    <tr>
                        <td>{{$proyecto->Emp_ID}}</td>
                        <td>{{$proyecto->Codigo}}</td>
                        <td>{{$proyecto->Nombre}}</td>
                        <td>{{$proyecto->Pro_ID}}</td>
                        <td>{{$proyecto->Nombre}}</td>
                        <td>{{$proyecto->Nombre_Tipo}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>