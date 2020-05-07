@extends('backend.menus.superior') 
@section('content-admin-css')
    <link href="{{ asset('css/backend/adminlte.min.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/backend/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/frontend/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/backend/estiloToggle.css') }}" type="text/css" rel="stylesheet" /> 
@stop

<section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-12">
          <h1>Lista Servicios</h1>
          </div>
          <div style="margin-top:15px; margin-left:15px">
          @can('completo')
            <button type="button" onclick="modalNuevo()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Nuevo Servicio
            </button>
          @endcan
          </div>
        </div>
      </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Servicios</h3>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div id="tablaDatatable">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- modal agregar -->
<div class="modal fade" id="modalAgregar">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Nuevo Servicio</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-nuevo">
                    <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Comision (No Decimales)</label>
                                            <input type="number" step="1" value="1" min="1" max="100" class="form-control" id="comision-nuevo">
                                        </div>

                                        <div class="form-group">
                                            <label>Nombre</label>
                                            <input type="text" maxlength="50" class="form-control" id="nombre-nuevo" placeholder="Nombre servicio">
                                        </div>
                                        <div class="form-group">
                                            <label>Identificador</label>
                                            <input type="text" maxlength="50" class="form-control" id="identificador-nuevo" placeholder="Identificador unico">
                                        </div>
                                        <div class="form-group">
                                            <label>Descripción corta No Mostrada en la App</label>
                                            <input type="text" maxlength="300" class="form-control" id="descripcion-nuevo" placeholder="Descripción servicio">
                                        </div>
                                        <div class="form-group">
                                            <label>Descripción Corta</label>
                                            <input type="text" maxlength="100" class="form-control" id="descripcioncorta-nuevo" placeholder="Descripción corta">
                                        </div>
                                        <div class="form-group">
                                            <div>
                                                <label>Logo</label>
                                                <p>Tamaño recomendado de: 100 x 100</p>
                                            </div>
                                            <br>
                                            <div class="col-md-10">
                                                <input type="file" style="color:#191818" id="imagenlogo-nuevo" accept="image/jpeg, image/jpg, image/png"/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div>
                                                <label>Imagen</label>
                                                <p>Tamaño recomendado de: 600 x -</p>
                                            </div> 
                                            <br>
                                            <div class="col-md-10">
                                                <input type="file" style="color:#191818" id="imagen-nuevo" accept="image/jpeg, image/jpg, image/png"/>
                                            </div>
                                        </div>
                                      
                                        <div class="form-group">
                                            <label>Utiliza mínimo (si esta activo, el minimo de compra sera necesario $)</label> 
                                            </br>
                                            <input type="checkbox" id="cbminimo">
                                        </div>
                                        <div class="form-group">
                                        <br>
                                            <label>Mínimo $ compra (para brindar el servicio adomicilio)</label>
                                            </br>
                                            <input type="number" value="0" step="any" id="minimocompra">
                                        </div>
                                        <div class="form-group">
                                            <label>Producto visible al Motorista (por ejemplo: farmacias, el motorista no vera el producto)</label>
                                            <br>
                                            <input type="checkbox" id="cbproducto">
                                        </div>

                                        <div class="form-group">
                                            <label>Este servicio es privado?</label>
                                            <br>
                                            <input type="checkbox" id="cbprivado">
                                        </div>
                                       
                                        <div class="form-group">
                                            <label style="color:#191818">Tipo Servicio</label>
                                            <br>
                                            <div>
                                                <select class="form-control" id="select-servicio">
                                                        <option value="0" selected>Seleccionar</option>
                                                    @foreach($tiposervicio as $item)                                                
                                                        <option value="{{$item->id}}">{{$item->nombre}}</option>
                                                    @endforeach   
                                                </select>
                                            </div> 
                                        </div> 
                                        <div class="form-group">
                                            <label>Teléfono</label>
                                            <input type="text" maxlength="20" class="form-control" id="telefono-nuevo" placeholder="Telefono">
                                        </div>
                                        <div class="form-group">
                                            <label>Latitud</label>
                                            <input type="text" maxlength="50" class="form-control" id="latitud-nuevo" placeholder="Latitud">
                                        </div>
                                        <div class="form-group">
                                            <label>Longitud</label>
                                            <input type="text" maxlength="50" class="form-control" id="longitud-nuevo" placeholder="Longitud">
                                        </div>
                                        <div class="form-group">
                                            <label>Dirección</label>
                                            <input type="text" maxlength="300" class="form-control" id="direccion-nuevo" placeholder="Direccion del servicio">
                                        </div>
                                        <div class="form-group">
                                            <label style="color:#191818">Tipo Vista</label>
                                            <br>
                                            <div>
                                                <select class="form-control" id="select-vista">
                                                    <option value="0" selected>Tipo Restaurante</option>
                                                    <option value="1">Tipo Tienda</option>
                                                </select>
                                            </div>
                                        </div> 
                                      
                                    </div> 
                                </div>
                                <div class="col-md-6">
                                    <div class="col-md-12">                                           
                                            <p>Horarios</p>

                                            <!-- horario abre y cierre -->
                                            <div class="form-group">
                                                <label>Cerrado lunes</label>
                                                <input type="checkbox" id="cbcerradolunes">
                                            </div>
                                            <div class="form-group">
                                                <label>Horario abre Lunes</label>
                                                <input type="time" class="form-control" id="horalunes1">
                                            </div>
                                            <div class="form-group">
                                                <label>Horario cierra Lunes</label>
                                                <input type="time" class="form-control" id="horalunes2">
                                            </div>
                                            <div class="form-group">
                                                <label>Usa la segunda hora</label>
                                                <input type="checkbox" id="cblunessegunda">
                                            </div>
                                            <div class="form-group">
                                                <label>Horario abre de nuevo Lunes</label>
                                                <input type="time" class="form-control" id="horalunes3">
                                            </div>
                                            <div class="form-group">
                                                <label>Horario cierra de nuevo Lunes</label>
                                                <input type="time" class="form-control" id="horalunes4">
                                            </div>

                                            <div class="form-group">
                                                <label>Cerrado martes</label>
                                                <input type="checkbox" id="cbcerradomartes">
                                            </div>
                                            <div class="form-group">
                                                <label>Horario abre Martes</label>
                                                <input type="time" class="form-control" id="horamartes1">
                                            </div>
                                            <div class="form-group">
                                                <label>Horario cierra Martes</label>
                                                <input type="time" class="form-control" id="horamartes2">
                                            </div>
                                            <div class="form-group">
                                                <label>Usa la segunda hora</label>
                                                <input type="checkbox" id="cbmartessegunda">
                                            </div>
                                            <div class="form-group">
                                                <label>Horario abre de nuevo Martes</label>
                                                <input type="time" class="form-control" id="horamartes3">
                                            </div>
                                            <div class="form-group">
                                                <label>Horario cierra de nuevo Martes</label>
                                                <input type="time" class="form-control" id="horamartes4">
                                            </div>

                                            <div class="form-group">
                                                <label>Cerrado miercoles</label>
                                                <input type="checkbox" id="cbcerradomiercoles">
                                            </div>
                                            <div class="form-group">
                                                <label>Horario abre Miercoles</label>
                                                <input type="time" class="form-control" id="horamiercoles1">
                                            </div>
                                            <div class="form-group">
                                                <label>Horario cierra Miercoles</label>
                                                <input type="time" class="form-control" id="horamiercoles2">
                                            </div>
                                            <div class="form-group">
                                                <label>Usa la segunda hora</label>
                                                <input type="checkbox" id="cbmiercolessegunda">
                                            </div>
                                            <div class="form-group">
                                                <label>Horario abre de nuevo Miercoles</label>
                                                <input type="time" class="form-control" id="horamiercoles3">
                                            </div>
                                            <div class="form-group">
                                                <label>Horario cierra de nuevo Miercoles</label>
                                                <input type="time" class="form-control" id="horamiercoles4">
                                            </div>

                                            <div class="form-group">
                                                <label>Cerrado jueves</label>
                                                <input type="checkbox" id="cbcerradojueves">
                                            </div>
                                            <div class="form-group">
                                                <label>Horario abre Jueves</label>
                                                <input type="time" class="form-control" id="horajueves1">
                                            </div>
                                            <div class="form-group">
                                                <label>Horario cierra Jueves</label>
                                                <input type="time" class="form-control" id="horajueves2">
                                            </div>
                                            <div class="form-group">
                                                <label>Usa la segunda hora</label>
                                                <input type="checkbox" id="cbjuevessegunda">
                                            </div>
                                            <div class="form-group">
                                                <label>Horario abre de nuevo Jueves</label>
                                                <input type="time" class="form-control" id="horajueves3">
                                            </div>
                                            <div class="form-group">
                                                <label>Horario cierra de nuevo Jueves</label>
                                                <input type="time" class="form-control" id="horajueves4">
                                            </div>


                                            <div class="form-group">
                                                <label>Cerrado viernes</label>
                                                <input type="checkbox" id="cbcerradoviernes">
                                            </div>
                                            <div class="form-group">
                                                <label>Horario abre Viernes</label>
                                                <input type="time" class="form-control" id="horaviernes1">
                                            </div>
                                            <div class="form-group">
                                                <label>Horario cierra Viernes</label>
                                                <input type="time" class="form-control" id="horaviernes2">
                                            </div>
                                            <div class="form-group">
                                                <label>Usa la segunda hora</label>
                                                <input type="checkbox" id="cbviernessegunda">
                                            </div>
                                            <div class="form-group">
                                                <label>Horario abre de nuevo Viernes</label>
                                                <input type="time" class="form-control" id="horaviernes3">
                                            </div>
                                            <div class="form-group">
                                                <label>Horario cierra de nuevo Viernes</label>
                                                <input type="time" class="form-control" id="horaviernes4">
                                            </div>

                                            <div class="form-group">
                                                <label>Cerrado Sabado</label>
                                                <input type="checkbox" id="cbcerradosabado">
                                            </div>
                                            <div class="form-group">
                                                <label>Horario abre Sabado</label>
                                                <input type="time" class="form-control" id="horasabado1">
                                            </div>
                                            <div class="form-group">
                                                <label>Horario cierra Sabado</label>
                                                <input type="time" class="form-control" id="horasabado2">
                                            </div>
                                            <div class="form-group">
                                                <label>Usa la segunda hora</label>
                                                <input type="checkbox" id="cbsabadosegunda">
                                            </div>
                                            <div class="form-group">
                                                <label>Horario abre de nuevo Sabado</label>
                                                <input type="time" class="form-control" id="horasabado3">
                                            </div>
                                            <div class="form-group">
                                                <label>Horario cierra de nuevo Sabado</label>
                                                <input type="time" class="form-control" id="horasabado4">
                                            </div>

                                            <div class="form-group">
                                                <label>Cerrado Domingo</label>
                                                <input type="checkbox" id="cbcerradodomingo">
                                            </div>
                                            <div class="form-group">
                                                <label>Horario abre Domingo</label>
                                                <input type="time" class="form-control" id="horadomingo1">
                                            </div>
                                            <div class="form-group">
                                                <label>Horario cierra Domingo</label>
                                                <input type="time" class="form-control" id="horadomingo2">
                                            </div>
                                            <div class="form-group">
                                                <label>Usa la segunda hora</label>
                                                <input type="checkbox" id="cbdomingosegunda">
                                            </div>
                                            <div class="form-group">
                                                <label>Horario abre de nuevo Domingo</label>
                                                <input type="time" class="form-control" id="horadomingo3">
                                            </div>
                                            <div class="form-group">
                                                <label>Horario cierra de nuevo Domingo</label>
                                                <input type="time" class="form-control" id="horadomingo4">
                                            </div>                                        
                                    </div>    
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="nuevo()">Guardar</button>
            </div>          
        </div>        
    </div>      
</div>


<!-- opciones de modales -->
<div class="modal fade" id="modalOpcion">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Opciones de Edicion</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-opciones">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12"> 
                            <input type="hidden" id="id-editar">
                                <div class="form-group">
                                    <button class="form-control" type="button" onclick="modalServicio()" class="btn btn-info btn-sm">
                                        <i class="fas fa-pencil-alt"></i>
                                            Editar Servicio
                                    </button>
                                </div>
                                
                                <div class="form-group">
                                    <button class="form-control" type="button" onclick="modalHorario()" class="btn btn-info btn-sm">
                                        <i class="fas fa-pencil-alt"></i>
                                            Editar Horario
                                    </button>
                                </div>                                
                            </div>
                        </div>
                    </div>
                </form>
            </div>
           
        </div>        
    </div>      
</div>

<!-- modal editar servicio-->
<div class="modal fade" id="modalServicio">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar servicio</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-servicio">
                    <div class="card-body">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Comision</label>
                                <input type="number" step="0.01" class="form-control" id="comision-editar">
                            </div>
                           
                            <div class="form-group">
                                <label>Nombre</label>
                                <input type="text" maxlength="50" class="form-control" id="nombre-editar" placeholder="Nombre servicio">
                            </div>
                            <div class="form-group">
                                <label>Identificador</label>
                                <input type="text" maxlength="50" class="form-control" id="identificador-editar" placeholder="Identificador unico">
                            </div>
                            <div class="form-group">
                                <label>Descripción del servicio</label>
                                <input type="text" maxlength="300" class="form-control" id="descripcion-editar" placeholder="Descripción servicio">
                            </div>
                            <div class="form-group">
                                <label>Descripción (No mostrada en la App)</label>
                                <input type="text" maxlength="100" class="form-control" id="descripcioncorta-editar" placeholder="Descripción corta">
                            </div>
                            <div class="form-group">
                                <div>
                                    <label>Logo</label>
                                    <p>Tamaño recomendado de: 100 x 100</p>
                                </div>
                                <br>
                                <div class="col-md-10">
                                    <input type="file" style="color:#191818" id="imagenlogo-editar" accept="image/jpeg, image/jpg, image/png"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <div>
                                    <label>Imagen</label>
                                    <p>Tamaño recomendado de: 600 x -</p>
                                </div> 
                                <br>
                                <div class="col-md-10">
                                    <input type="file" style="color:#191818" id="imagen-editar" accept="image/jpeg, image/jpg, image/png"/>
                                </div>
                            </div>
                       
                            <div class="form-group">
                                <label>Utiliza mínimo (si esta activo, el minimo de compra sera necesario $)</label>
                                <input type="checkbox" id="cbminimo-editar">
                            </div>
                            <div class="form-group">
                            <br>
                                <label>Mínimo $ compra (para brindar el servicio adomicilio)</label>
                                <input type="number" step="any" id="minimocompra-editar">
                            </div>
                            <div class="form-group">
                                <label>Producto visible al Motorista (por ejemplo: farmacias, el motorista no vera el producto)</label>
                                <input type="checkbox" id="cbproducto-editar">
                            </div>
                           
                            
                            <div class="form-group">
                                <label>Teléfono</label>
                                <input type="text" maxlength="20" class="form-control" id="telefono-editar" placeholder="Telefono">
                            </div>
                            <div class="form-group">
                                <label>Latitud</label>
                                <input type="text" maxlength="50" class="form-control" id="latitud-editar" placeholder="Latitud">
                            </div>
                            <div class="form-group">
                                <label>Longitud</label>
                                <input type="text" maxlength="50" class="form-control" id="longitud-editar" placeholder="Longitud">
                            </div>
                            <div class="form-group">
                                <label>Dirección</label>
                                <input type="text" maxlength="300" class="form-control" id="direccion-editar" placeholder="Direccion del servicio">
                            </div>
                            <div class="form-group">
                                <label style="color:#191818">Tipo Vista</label>
                                <br>
                                <div>
                                    <select class="form-control" id="select-vista-editar">
                                        <option value="0" selected>Tipo Restaurante</option>
                                        <option value="1">Tipo Tienda</option>
                                    </select>
                                </div>
                            </div> 

                            <div class="form-group">
                                <label style="color:#191818">Tipo Servicio</label>
                                <br>
                                <div>
                                    <select class="form-control" id="select-servicio-editar">                                     
                                    </select>
                                </div> 
                            </div>
                           
                            <div class="form-group">
                                <label>Cerrado emergencia</label>
                                <br>
                                <input type="checkbox" id="cbcerradoemergencia-editar">
                            </div>

                            <div class="form-group">
                                <label>Activo</label>
                                <br>
                                <input type="checkbox" id="cbactivo-editar">
                            </div>

                            <div class="form-group">
                                <label>Este servicio es privado?</label>
                                <br>
                                <input type="checkbox" id="cbprivado-editar">
                            </div>
                        </div> 
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                @can('completo')
                    <button type="button" class="btn btn-primary" onclick="editarservicio()">Guardar</button>
                @endcan
            </div>          
        </div>        
    </div>      
</div>

<!-- modal editar horarios-->
<div class="modal fade" id="modalHorario">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar Horarios</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-horario">
                    <div class="card-body">
                        <div class="col-md-12">

                             <!-- horario abre y cierre -->
                             <div class="form-group">
                                <label>Cerrado lunes</label>
                                <input type="checkbox" id="cbcerradolunes-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario abre Lunes</label>
                                <input type="time" class="form-control" id="horalunes1-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario cierra Lunes</label>
                                <input type="time" class="form-control" id="horalunes2-editar">
                            </div>
                            <div class="form-group">
                                <label>Usa la segunda hora</label>
                                <input type="checkbox" id="cblunessegunda-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario abre de nuevo Lunes</label>
                                <input type="time" class="form-control" id="horalunes3-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario cierra de nuevo Lunes</label>
                                <input type="time" class="form-control" id="horalunes4-editar">
                            </div>

                            <div class="form-group">
                                <label>Cerrado martes</label>
                                <input type="checkbox" id="cbcerradomartes-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario abre Martes</label>
                                <input type="time" class="form-control" id="horamartes1-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario cierra Martes</label>
                                <input type="time" class="form-control" id="horamartes2-editar">
                            </div>
                            <div class="form-group">
                                <label>Usa la segunda hora</label>
                                <input type="checkbox" id="cbmartessegunda-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario abre de nuevo Martes</label>
                                <input type="time" class="form-control" id="horamartes3-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario cierra de nuevo Martes</label>
                                <input type="time" class="form-control" id="horamartes4-editar">
                            </div>

                            <div class="form-group">
                                <label>Cerrado miercoles</label>
                                <input type="checkbox" id="cbcerradomiercoles-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario abre Miercoles</label>
                                <input type="time" class="form-control" id="horamiercoles1-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario cierra Miercoles</label>
                                <input type="time" class="form-control" id="horamiercoles2-editar">
                            </div>
                            <div class="form-group">
                                <label>Usa la segunda hora</label>
                                <input type="checkbox" id="cbmiercolessegunda-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario abre de nuevo Miercoles</label>
                                <input type="time" class="form-control" id="horamiercoles3-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario cierra de nuevo Miercoles</label>
                                <input type="time" class="form-control" id="horamiercoles4-editar">
                            </div>

                            <div class="form-group">
                                <label>Cerrado jueves</label>
                                <input type="checkbox" id="cbcerradojueves-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario abre Jueves</label>
                                <input type="time" class="form-control" id="horajueves1-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario cierra Jueves</label>
                                <input type="time" class="form-control" id="horajueves2-editar">
                            </div>
                            <div class="form-group">
                                <label>Usa la segunda hora</label>
                                <input type="checkbox" id="cbjuevessegunda-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario abre de nuevo Jueves</label>
                                <input type="time" class="form-control" id="horajueves3-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario cierra de nuevo Jueves</label>
                                <input type="time" class="form-control" id="horajueves4-editar">
                            </div>


                            <div class="form-group">
                                <label>Cerrado viernes</label>
                                <input type="checkbox" id="cbcerradoviernes-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario abre Viernes</label>
                                <input type="time" class="form-control" id="horaviernes1-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario cierra Viernes</label>
                                <input type="time" class="form-control" id="horaviernes2-editar">
                            </div>
                            <div class="form-group">
                                <label>Usa la segunda hora</label>
                                <input type="checkbox" id="cbviernessegunda-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario abre de nuevo Viernes</label>
                                <input type="time" class="form-control" id="horaviernes3-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario cierra de nuevo Viernes</label>
                                <input type="time" class="form-control" id="horaviernes4-editar">
                            </div>

                            <div class="form-group">
                                <label>Cerrado Sabado</label>
                                <input type="checkbox" id="cbcerradosabado-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario abre Sabado</label>
                                <input type="time" class="form-control" id="horasabado1-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario cierra Sabado</label>
                                <input type="time" class="form-control" id="horasabado2-editar">
                            </div>
                            <div class="form-group">
                                <label>Usa la segunda hora</label>
                                <input type="checkbox" id="cbsabadosegunda-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario abre de nuevo Sabado</label>
                                <input type="time" class="form-control" id="horasabado3-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario cierra de nuevo Sabado</label>
                                <input type="time" class="form-control" id="horasabado4-editar">
                            </div>

                            <div class="form-group">
                                <label>Cerrado Domingo</label>
                                <input type="checkbox" id="cbcerradodomingo-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario abre Domingo</label>
                                <input type="time" class="form-control" id="horadomingo1-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario cierra Domingo</label>
                                <input type="time" class="form-control" id="horadomingo2-editar">
                            </div>
                            <div class="form-group">
                                <label>Usa la segunda hora</label>
                                <input type="checkbox" id="cbdomingosegunda-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario abre de nuevo Lunes</label>
                                <input type="time" class="form-control" id="horadomingo3-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario cierra de nuevo Domingo</label>
                                <input type="time" class="form-control" id="horadomingo4-editar">
                            </div>
                        
                        </div> 
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                @can('completo')
                    <button type="button" class="btn btn-primary" onclick="editarHoras()">Guardar</button>
                @endcan
            </div>          
        </div>        
    </div>      
</div>



@extends('backend.menus.inferior')
@section('content-admin-js')  

<script src="{{ asset('js/backend/jquery.dataTables.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/backend/dataTables.bootstrap4.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/frontend/toastr.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/frontend/axios.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/frontend/loadingOverlay.js') }}" type="text/javascript"></script>

 <script type="text/javascript">
    $(document).ready(function(){
      var ruta = "{{ URL::to('admin/servicios/tabla/lista') }}";
      $('#tablaDatatable').load(ruta);
    });

 </script>

<script>

    // abrir modal
    function modalNuevo(){
        document.getElementById("formulario-nuevo").reset();
        $('#modalAgregar').modal('show');
    } 

    //nuevo servicio
    function nuevo(){
        
        // parte 1
        var comision = document.getElementById('comision-nuevo').value;
      
        var nombre = document.getElementById('nombre-nuevo').value; 
        var identificador = document.getElementById('identificador-nuevo').value;
        var descripcion = document.getElementById('descripcion-nuevo').value;
        var descripcioncorta = document.getElementById('descripcioncorta-nuevo').value;
        var logo = document.getElementById('imagenlogo-nuevo'); 
        var imagen = document.getElementById('imagen-nuevo'); 
        var tiposervicio = document.getElementById('select-servicio').value;
        var telefono = document.getElementById('telefono-nuevo').value;
        var latitud = document.getElementById('latitud-nuevo').value;
        var longitud = document.getElementById('longitud-nuevo').value;
        var direccion = document.getElementById('direccion-nuevo').value;
        var tipovista = document.getElementById('select-vista').value;
        
        var cbprivado = document.getElementById('cbprivado').checked;
       
        var cbminimo = document.getElementById('cbminimo').checked;
        var minimocompra = document.getElementById('minimocompra').value;
        var cbproducto = document.getElementById('cbproducto').checked;
             
        var horalunes1 = document.getElementById('horalunes1').value;
        var horalunes2 = document.getElementById('horalunes2').value;
        var horalunes3 = document.getElementById('horalunes3').value;
        var horalunes4 = document.getElementById('horalunes4').value; 
        var cblunessegunda = document.getElementById('cblunessegunda').checked;
        var cbcerradolunes = document.getElementById('cbcerradolunes').checked;

        var horamartes1 = document.getElementById('horamartes1').value;
        var horamartes2 = document.getElementById('horamartes2').value;
        var horamartes3 = document.getElementById('horamartes3').value;
        var horamartes4 = document.getElementById('horamartes4').value;
        var cbmartessegunda = document.getElementById('cbmartessegunda').checked;
        var cbcerradomartes = document.getElementById('cbcerradomartes').checked;

        var horamiercoles1 = document.getElementById('horamiercoles1').value;
        var horamiercoles2 = document.getElementById('horamiercoles2').value;
        var horamiercoles3 = document.getElementById('horamiercoles3').value;
        var horamiercoles4 = document.getElementById('horamiercoles4').value;
        var cbmiercolessegunda = document.getElementById('cbmiercolessegunda').checked;
        var cbcerradomiercoles = document.getElementById('cbcerradomiercoles').checked;
        
        var horajueves1 = document.getElementById('horajueves1').value;
        var horajueves2 = document.getElementById('horajueves2').value;
        var horajueves3 = document.getElementById('horajueves3').value;
        var horajueves4 = document.getElementById('horajueves4').value;
        var cbjuevessegunda = document.getElementById('cbjuevessegunda').checked; 
        var cbcerradojueves = document.getElementById('cbcerradojueves').checked;

        var horaviernes1 = document.getElementById('horaviernes1').value;
        var horaviernes2 = document.getElementById('horaviernes2').value;
        var horaviernes3 = document.getElementById('horaviernes3').value;
        var horaviernes4 = document.getElementById('horaviernes4').value;
        var cbviernessegunda = document.getElementById('cbviernessegunda').checked;
        var cbcerradoviernes = document.getElementById('cbcerradoviernes').checked;

        var horasabado1 = document.getElementById('horasabado1').value;
        var horasabado2 = document.getElementById('horasabado2').value;
        var horasabado3 = document.getElementById('horasabado3').value;
        var horasabado4 = document.getElementById('horasabado4').value;
        var cbsabadosegunda = document.getElementById('cbsabadosegunda').checked;
        var cbcerradosabado = document.getElementById('cbcerradosabado').checked;

        var horadomingo1 = document.getElementById('horadomingo1').value;
        var horadomingo2 = document.getElementById('horadomingo2').value;
        var horadomingo3 = document.getElementById('horadomingo3').value; 
        var horadomingo4 = document.getElementById('horadomingo4').value;
        var cbdomingosegunda = document.getElementById('cbdomingosegunda').checked;
        var cbcerradodomingo = document.getElementById('cbcerradodomingo').checked;
        
        var retorno1 = validacionNuevo(comision, nombre, identificador, descripcion, descripcioncorta, logo, imagen, tiposervicio,
         telefono, latitud, longitud, direccion);

        if(!retorno1){
            return;
        }

         var retorno2 = validacionNuevo2(horalunes1, horalunes2, horalunes3, horalunes4, horamartes1, horamartes2, horamartes3, horamartes4,
         horamiercoles1, horamiercoles2, horamiercoles3, horamiercoles4, horajueves1, horajueves2, horajueves3, horajueves4, horaviernes1, horaviernes2, horaviernes3, horaviernes4,
         horasabado1, horasabado2, horasabado3, horasabado4, horadomingo1, horadomingo2, horadomingo3, horadomingo4);

        if(retorno1 && retorno2){

           
            var cbminimo_1 = 0;
            var cbproducto_1 = 0;
                        
            var cblunessegunda_1 = 0;           
            var cbcerradolunes_1 = 0;
            var cbmartessegunda_1 = 0;           
            var cbcerradomartes_1 = 0;
            var cbmiercolessegunda_1 = 0;           
            var cbcerradomiercoles_1 = 0;
            var cbjuevessegunda_1 = 0;           
            var cbcerradojueves_1 = 0;
            var cbviernessegunda_1 = 0;           
            var cbcerradoviernes_1 = 0;
            var cbsabadosegunda_1 = 0;           
            var cbcerradosabado_1 = 0;
            var cbdomingosegunda_1 = 0;           
            var cbcerradodomingo_1 = 0;
            var cbprivado_1 = 0

            if(cbprivado){
                cbprivado_1 = 1;
            }

            if(minimocompra === ''){
                minimocompra = 0;
            }

          
            if(cbminimo){
                cbminimo_1 = 1;
            }

            if(cbproducto){
                cbproducto_1 = 1;
            }


            //--

            if(cblunessegunda){
                cblunessegunda_1 = 1;
            }

            if(cbcerradolunes){
                cbcerradolunes_1 = 1;
            }

            if(cbmartessegunda){
                cbmartessegunda_1 = 1;
            }

            if(cbcerradomartes){
                cbcerradomartes_1 = 1;
            }

            if(cbmiercolessegunda){
                cbmiercolessegunda_1 = 1;
            }

            if(cbcerradomiercoles){
                cbcerradomiercoles_1 = 1;
            }

            if(cbjuevessegunda){
                cbjuevessegunda_1 = 1;
            }

            if(cbcerradojueves){
                cbcerradojueves_1 = 1;
            }

            if(cbviernessegunda){
                cbviernessegunda_1 = 1;
            }

            if(cbcerradoviernes){
                cbcerradoviernes_1 = 1;
            }

            if(cbsabadosegunda){
                cbsabadosegunda_1 = 1;
            }

            if(cbcerradosabado){
                cbcerradosabado_1 = 1;
            }

            if(cbdomingosegunda){
                cbdomingosegunda_1 = 1;
            }

            if(cbcerradodomingo){
                cbcerradodomingo_1 = 1;
            }

            var spinHandle = loadingOverlay().activate();             
            var formData = new FormData();
            formData.append('comision', comision);
            formData.append('nombre', nombre);
            formData.append('identificador', identificador);
            formData.append('descripcion', descripcion);
            formData.append('descripcioncorta', descripcioncorta);
            formData.append('logo', logo.files[0]);
            formData.append('imagen', imagen.files[0]);
            formData.append('tiposervicio', tiposervicio);
            formData.append('telefono', telefono);
            formData.append('latitud', latitud);
            formData.append('longitud', longitud);
            formData.append('direccion', direccion);
            formData.append('tipovista', tipovista);
            
            formData.append('cbminimo', cbminimo_1);
            formData.append('minimocompra', minimocompra);
            formData.append('cbproducto', cbproducto_1);
            formData.append('cbprivado', cbprivado_1);
            
            formData.append('horalunes1', horalunes1);
            formData.append('horalunes2', horalunes2);
            formData.append('horalunes3', horalunes3);
            formData.append('horalunes4', horalunes4);
            formData.append('cblunessegunda', cblunessegunda_1);
            formData.append('cbcerradolunes', cbcerradolunes_1);

            formData.append('horamartes1', horamartes1);
            formData.append('horamartes2', horamartes2);
            formData.append('horamartes3', horamartes3);
            formData.append('horamartes4', horamartes4);
            formData.append('cbmartessegunda', cbmartessegunda_1);
            formData.append('cbcerradomartes', cbcerradomartes_1);

            //---

            formData.append('horamiercoles1', horamiercoles1);
            formData.append('horamiercoles2', horamiercoles2);
            formData.append('horamiercoles3', horamiercoles3);
            formData.append('horamiercoles4', horamiercoles4);
            formData.append('cbmiercolessegunda', cbmiercolessegunda_1);
            formData.append('cbcerradomiercoles', cbcerradomiercoles_1);

            //-- 

            formData.append('horajueves1', horajueves1);
            formData.append('horajueves2', horajueves2);
            formData.append('horajueves3', horajueves3);
            formData.append('horajueves4', horajueves4);
            formData.append('cbjuevessegunda', cbjuevessegunda_1);
            formData.append('cbcerradojueves', cbcerradojueves_1);

            formData.append('horaviernes1', horaviernes1);
            formData.append('horaviernes2', horaviernes2);
            formData.append('horaviernes3', horaviernes3);
            formData.append('horaviernes4', horaviernes4);
            formData.append('cbviernessegunda', cbviernessegunda_1);
            formData.append('cbcerradoviernes', cbcerradoviernes_1);

            //---

            formData.append('horasabado1', horasabado1);
            formData.append('horasabado2', horasabado2);
            formData.append('horasabado3', horasabado3);
            formData.append('horasabado4', horasabado4);
            formData.append('cbsabadosegunda', cbsabadosegunda_1);
            formData.append('cbcerradosabado', cbcerradosabado_1);

            formData.append('horadomingo1', horadomingo1);
            formData.append('horadomingo2', horadomingo2);
            formData.append('horadomingo3', horadomingo3);
            formData.append('horadomingo4', horadomingo4);
            formData.append('cbdomingosegunda', cbdomingosegunda_1);
            formData.append('cbcerradodomingo', cbcerradodomingo_1);

            axios.post('/admin/servicios/nuevo', formData, {
            })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
                
                console.log(response);
                respuestaNuevo(response);               
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle);
                toastr.error('Error');
            });
        }
    }

    // respuesta al agregar
    function respuestaNuevo(response){

        if (response.data.success == 0) {
            toastr.error('Validacion incorrecta');
        } else if (response.data.success == 1) {
            toastr.success('Servicio Agregado');
            var ruta = "{{ URL::to('admin/servicios/tabla/lista') }}";
            $('#tablaDatatable').load(ruta);
            $('#modalAgregar').modal('hide');
        } else if (response.data.success == 2) {
            toastr.error('Error al agregar imagen');
        } else if(response.data.success == 3){
            toastr.error('Error del try catch');
        } else if(response.data.success == 4){
            toastr.error('Identificador no disponible');
        }
        else {
            toastr.error('Error desconocido');
        }
    }
 
    // validar datos a guardar
    function validacionNuevo(comision, nombre, identificador, descripcion, descripcioncorta, logo, imagen, tiposervicio,
     telefono, latitud, longitud, direccion){

        if (comision === '') {
            toastr.error('Comision es requerido');
            return false;
        }

        if(comision > 100){
            toastr.error('Comision maximo es 100');
            return false;
        }

        if(comision < 0){
            toastr.error('Comision minima es 0');
            return false;
        }
        
        if (nombre === '') {
            toastr.error("Nombre es requerido");
            return false;
        }

        if(nombre.length > 50){
            toastr.error("50 caracter máximo nombre");
            return false;
        }

        if (identificador === '') {
            toastr.error("Identificador es requerido");
            return false;
        }

        if(identificador.length > 50){
            toastr.error("50 caracter máximo identificador");
            return false;
        }

        if (descripcion === '') {
            toastr.error("descripcion es requerido");
            return false;
        }

        if(descripcion.length > 300){
            toastr.error("300 caracter máximo descripcion");
            return false;
        }

        if (descripcioncorta === '') {
            toastr.error("descripcion corta es requerido");
            return false;
        }

        if(descripcioncorta.length > 100){
            toastr.error("50 caracter máximo descripcion corta");
            return false;
        }
        
        if(logo.files && logo.files[0]){ // si trae imagen
            if (!logo.files[0].type.match('image/jpeg|image/jpeg|image/png')){      
                toastr.error('Formato de imagen permitido: .png .jpg .jpeg');
                return false;       
            } 
        }else{
            toastr.error("logo es requerido");
            return false;
        }

        if(imagen.files && imagen.files[0]){ // si trae imagen
            if (!imagen.files[0].type.match('image/jpeg|image/jpeg|image/png')){      
                toastr.error('Formato de imagen permitido: .png .jpg .jpeg');
                return false;       
            } 
        }else{
            toastr.error("imagen es requerido");
            return false;
        }

        if(tiposervicio == 0){
            toastr.error("Seleccionar servicio");
            return false;
        }

        if (telefono === '') {
            toastr.error("telefono es requerido");
            return false;
        }

        if(telefono.length > 20){
            toastr.error("20 caracter máximo telefono");
            return false;
        }

        if (latitud === '') {
            toastr.error("latitud es requerido");
            return false;
        }

        if(latitud.length > 50){
            toastr.error("50 caracter máximo latitud");
            return false;
        }

        if (longitud === '') {
            toastr.error("longitud es requerido");
            return false;
        }

        if(longitud.length > 50){
            toastr.error("50 caracter máximo longitud");
            return false;
        }

        if (direccion === '') {
            toastr.error("direccion es requerido");
            return false;
        }

        if(direccion.length > 300){
            toastr.error("300 caracter máximo direccion");
            return false;
        }
        
        return true;
    }

    function validacionNuevo2(horalunes1, horalunes2, horalunes3, horalunes4, horamartes1, horamartes2, horamartes3, horamartes4,
         horamiercoles1, horamiercoles2, horamiercoles3, horamiercoles4, horajueves1, horajueves2, horajueves3, horajueves4, horaviernes1, horaviernes2, horaviernes3, horaviernes4,
         horasabado1, horasabado2, horasabado3, horasabado4, horadomingo1, horadomingo2, horadomingo3, horadomingo4){

            if (horalunes1 === '') {
                toastr.error("lunes1 horario es requerido");
                return false;
            }

            if (horalunes2 === '') {
                toastr.error("lunes2 horario es requerido");
                return false;
            }

            if (horalunes3 === '') {
                toastr.error("lunes3 horario es requerido");
                return false;
            }

            if (horalunes4 === '') {
                toastr.error("lunes4 horario es requerido");
                return false;
            }

            //------

            if (horamartes1 === '') {
                toastr.error("martes1 horario es requerido");
                return false;
            }

            if (horamartes2 === '') {
                toastr.error("martes2 horario es requerido");
                return false;
            }

            if (horamartes3 === '') {
                toastr.error("martes3 horario es requerido");
                return false;
            }

            if (horamartes4 === '') {
                toastr.error("martes4 horario es requerido");
                return false;
            }

            //---

            if (horamiercoles1 === '') {
                toastr.error("miercoles1 horario es requerido");
                return false;
            }

            if (horamiercoles2 === '') {
                toastr.error("miercoles2 horario es requerido");
                return false;
            }

            if (horamiercoles3 === '') {
                toastr.error("miercoles3 horario es requerido");
                return false;
            }

            if (horamiercoles4 === '') {
                toastr.error("miercoles4 horario es requerido");
                return false;
            }

            //----

            if (horajueves1 === '') {
                toastr.error("jueves1 horario es requerido");
                return false;
            }

            if (horajueves2 === '') {
                toastr.error("jueves2 horario es requerido");
                return false;
            }

            if (horajueves3 === '') {
                toastr.error("jueves3 horario es requerido");
                return false;
            }

            if (horajueves4 === '') {
                toastr.error("jueves4 horario es requerido");
                return false;
            }

            //---

            if (horaviernes1 === '') {
                toastr.error("viernes1 horario es requerido");
                return false;
            }

            if (horaviernes2 === '') {
                toastr.error("viernes2 horario es requerido");
                return false;
            }

            if (horaviernes3 === '') {
                toastr.error("viernes3 horario es requerido");
                return false;
            }

            if (horaviernes4 === '') {
                toastr.error("viernes4 horario es requerido");
                return false;
            }

            //---

            if (horasabado1 === '') {
                toastr.error("sabado1 horario es requerido");
                return false;
            }

            if (horasabado2 === '') {
                toastr.error("sabado2 horario es requerido");
                return false;
            }

            if (horasabado3 === '') {
                toastr.error("sabado3 horario es requerido");
                return false;
            }

            if (horasabado4 === '') {
                toastr.error("sabado4 horario es requerido");
                return false;
            }

            //---

            if (horadomingo1 === '') {
                toastr.error("domingo1 horario es requerido");
                return false;
            }

            if (horadomingo2 === '') {
                toastr.error("domingo2 horario es requerido");
                return false;
            }

            if (horadomingo3 === '') {
                toastr.error("domingo3 horario es requerido");
                return false;
            }

            if (horadomingo4 === '') {
                toastr.error("domingo4 horario es requerido");
                return false;
            }

            return true;
    }

    // elegir modal para modificar
    function verModales(id){
        document.getElementById("formulario-opciones").reset();
        $('#id-editar').val(id);
        $('#modalOpcion').modal('show');
    } 

    // vista editar servicio
    function modalServicio(){

        document.getElementById("formulario-servicio").reset();
        
        var id = document.getElementById('id-editar').value;
        spinHandle = loadingOverlay().activate();

        axios.post('/admin/servicios/informacion/servicio',{
        'id': id 
            })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
                if(response.data.success == 1){
                     
                    $('#modalServicio').modal('show');
                    $('#comision-editar').val(response.data.servicio.comision);
                    $('#multa-editar').val(response.data.servicio.multa);
                    $('#nombre-editar').val(response.data.servicio.nombre);
                    $('#descripcion-editar').val(response.data.servicio.descripcion);
                    $('#descripcioncorta-editar').val(response.data.servicio.descripcion_corta);
                      
                  
                    if(response.data.servicio.utiliza_minimo == 1){
                        $('#cbminimo-editar').prop('checked', true);
                    }

                    if(response.data.servicio.privado == 1){
                        $('#cbprivado-editar').prop('checked', true);
                    }
                   
                    $('#minimocompra-editar').val(response.data.servicio.minimo);

                    if(response.data.servicio.producto_visible == 1){
                        $('#cbproducto-editar').prop('checked', true);
                    }

                    var tipo = document.getElementById("select-servicio-editar");
                    // limpiar select
                    document.getElementById("select-servicio-editar").options.length = 0;
                
                    $.each(response.data.tipo, function( key, val ){  
                       if(response.data.servicio.tipo_servicios_id == val.id){
                            $('#select-servicio-editar').append('<option value="' +val.id +'" selected="selected">'+val.nombre+'</option>');
                       }else{
                            $('#select-servicio-editar').append('<option value="' +val.id +'">'+val.nombre+'</option>');
                       }
                    });

                    if(response.data.servicio.tipo_vista == 1){
                        $('#select-vista-editar option')[1].selected = true;
                    }

                    if(response.data.servicio.cerrado_emergencia == 1){
                        $('#cbcerradoemergencia-editar').prop('checked', true);
                    }

                    if(response.data.servicio.activo == 1){
                        $('#cbactivo-editar').prop('checked', true);
                    }

                    $('#identificador-editar').val(response.data.servicio.identificador);
                    $('#telefono-editar').val(response.data.servicio.telefono);
                    $('#latitud-editar').val(response.data.servicio.latitud);
                    $('#longitud-editar').val(response.data.servicio.longitud);
                    $('#direccion-editar').val(response.data.servicio.direccion);
                    

                }else{
                    toastr.error('Tipo servicio no encontrado');
                }
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle);
                toastr.error('Error del servidor');
        });        
    }   

    // editar servicio
    function editarservicio(){

        var id = document.getElementById('id-editar').value;
        var comision = document.getElementById('comision-editar').value;
        var nombre = document.getElementById('nombre-editar').value;
        var identificador = document.getElementById('identificador-editar').value;
        var descripcion = document.getElementById('descripcion-editar').value;
        var descripcioncorta = document.getElementById('descripcioncorta-editar').value;
        var logo = document.getElementById('imagenlogo-editar'); 
        var imagen = document.getElementById('imagen-editar'); 
        var tiposervicio = document.getElementById('select-servicio-editar').value;
        var telefono = document.getElementById('telefono-editar').value;
        var latitud = document.getElementById('latitud-editar').value;
        var longitud = document.getElementById('longitud-editar').value;
        var direccion = document.getElementById('direccion-editar').value;
        var tipovista = document.getElementById('select-vista-editar').value;
        
        var cbminimo = document.getElementById('cbminimo-editar').checked;
        var minimocompra = document.getElementById('minimocompra-editar').value;
        var cbproducto = document.getElementById('cbproducto-editar').checked;
        var cbcerradoemergencia = document.getElementById('cbcerradoemergencia-editar').checked;
        var cbactivo = document.getElementById('cbactivo-editar').checked;
      
        var privado = document.getElementById('cbprivado-editar').checked;

        var retorno = validarservicio(comision, nombre, descripcion, descripcioncorta, logo, imagen,
     telefono, latitud, longitud, direccion, minimocompra, identificador);

     if(retorno){

      
        var cbminimo_1 = 0;
        var cbproducto_1 = 0;
        var cbcerradoemergencia_1 = 0;
        var activo = 0;
      
        var privado_1 = 0;

        if(privado){
            privado_1 = 1;
        }

       

        if(cbminimo){
            cbminimo_1 = 1;
        }
 
        if(cbproducto){
            cbproducto_1 = 1;
        }


        if(cbcerradoemergencia){
            cbcerradoemergencia_1 = 1;
        }
 
        if(cbactivo){
            activo = 1;
        }

        var spinHandle = loadingOverlay().activate();
        var formData = new FormData();
        formData.append('id', id);
        formData.append('comision', comision);
        formData.append('nombre', nombre);
        formData.append('identificador', identificador);
        formData.append('descripcion', descripcion);
        formData.append('descripcioncorta', descripcioncorta);
        formData.append('logo', logo.files[0]);
        formData.append('imagen', imagen.files[0]);
        formData.append('tiposervicio', tiposervicio);
        formData.append('telefono', telefono);
        formData.append('latitud', latitud);
        formData.append('longitud', longitud);
        formData.append('direccion', direccion);
        formData.append('tipovista', tipovista);
        
        formData.append('cbminimo', cbminimo_1);
        formData.append('minimocompra', minimocompra);
        formData.append('cbproducto', cbproducto_1);
        formData.append('cbcerradoemergencia', cbcerradoemergencia_1);
        formData.append('cbactivo', activo);
        formData.append('privado', privado_1);

        axios.post('/admin/servicios/editar-servicio', formData, {
            })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);

                respuestaEditarServicio(response);
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle);
                toastr.error('Error');
            });
     }
    }

    function respuestaEditarServicio(response){
        if (response.data.success == 0) {
            toastr.error('Validacion incorrecta');
        } else if (response.data.success == 1) {
            toastr.error('error al validar imagen'); 
        } else if (response.data.success == 2) {
            toastr.error('error al subir imagen');
        } else if (response.data.success == 3) {
            toastr.success('Servicio Actualizado');
            var ruta = "{{ URL::to('admin/servicios/tabla/lista') }}";
            $('#tablaDatatable').load(ruta);
            $('#modalServicio').modal('hide');
        } else if(response.data.success == 4){
            toastr.error('Error trycatch');
        }else if(response.data.success == 5){
            toastr.error('identificador no disponible');
        }
        else {
            toastr.error('Error desconocido');
        }
    }

    function validarservicio(comision, nombre, descripcion, descripcioncorta, logo, imagen,
     telefono, latitud, longitud, direccion, minimocompra, identificador){
        
        if (comision === '') {
            toastr.error("comision es requerido");
            return false;
        }

        if(comision > 100){
            toastr.error("comision maxima es 100");
            return false;
        }

        if(comision < 0){
            toastr.error("comision minima es 0");
            return false;
        }

        if (nombre === '') {
            toastr.error("Nombre es requerido");
            return false;
        }

        if(nombre.length > 50){
            toastr.error("50 caracter máximo nombre");
            return false;
        }
        
        if (descripcion === '') {
            toastr.error("descripcion es requerido");
            return false;
        }

        if(descripcion.length > 300){
            toastr.error("300 caracter máximo descripcion");
            return false;
        }

        if (descripcioncorta === '') {
            toastr.error("descripcion corta es requerido");
            return false;
        }

        if(descripcioncorta.length > 100){
            toastr.error("100 caracter máximo descripcion corta");
            return false;
        }
  
        if(logo.files && logo.files[0]){ // si trae imagen
            if (!logo.files[0].type.match('image/jpeg|image/jpeg|image/png')){      
                toastr.error('Formato de imagen permitido: .png .jpg .jpeg');
                return false;       
            } 
        }

        if(imagen.files && imagen.files[0]){ // si trae imagen
            if (!imagen.files[0].type.match('image/jpeg|image/jpeg|image/png')){      
                toastr.error('Formato de imagen permitido: .png .jpg .jpeg');
                return false;       
            } 
        }
        
        if (telefono === '') {
            toastr.error("telefono es requerido");
            return false;
        }

        if(telefono.length > 20){
            toastr.error("20 caracter máximo telefono");
            return false;
        }

        if (latitud === '') {
            toastr.error("latitud es requerido");
            return false;
        }

        if(latitud.length > 50){
            toastr.error("50 caracter máximo latitud");
            return false;
        }

        if (longitud === '') {
            toastr.error("longitud es requerido");
            return false;
        }

        if(longitud.length > 50){
            toastr.error("50 caracter máximo longitud");
            return false;
        }

        if(identificador.length > 50){
            toastr.error("50 caracter máximo identificador11");
            return false;
        }
        
        if (direccion === '') {
            toastr.error("direccion es requerido");
            return false;
        }

        if(direccion.length > 300){
            toastr.error("300 caracter máximo direccion");
            return false;
        }
        
        if (minimocompra === '') {
            toastr.error('Agregar minimo de compra');
            return false;
        }

        if (identificador === '') {
            toastr.error("identificador es requerido");
            return false;
        }

        return true;
    }


    // vista editar horarios
    function modalHorario(){
        document.getElementById("formulario-horario").reset();

        var id = document.getElementById('id-editar').value;
        spinHandle = loadingOverlay().activate();

        axios.post('/admin/servicios/informacion-horario/servicio',{
        'id': id
            })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
                if(response.data.success = 1){
                    
                    $('#modalHorario').modal('show');
                
                    $.each(response.data.horario, function( key, val ){  
                        if(val.dia == 1){ //domingo
                            $('#horadomingo1-editar').val(val.hora1);
                            $('#horadomingo2-editar').val(val.hora2);
                            $('#horadomingo3-editar').val(val.hora3);
                            $('#horadomingo4-editar').val(val.hora4);

                            if(val.segunda_hora == 1){
                                $('#cbdomingosegunda-editar').prop('checked', true);
                            }

                            if(val.cerrado == 1){
                                $('#cbcerradodomingo-editar').prop('checked', true);
                            }

                        }else if(val.dia == 2){
                            $('#horalunes1-editar').val(val.hora1);
                            $('#horalunes2-editar').val(val.hora2);
                            $('#horalunes3-editar').val(val.hora3);
                            $('#horalunes4-editar').val(val.hora4);

                            if(val.segunda_hora == 1){
                                $('#cblunessegunda-editar').prop('checked', true);
                            }

                            if(val.cerrado == 1){
                                $('#cbcerradolunes-editar').prop('checked', true);
                            }
                        }else if(val.dia == 3){
                            $('#horamartes1-editar').val(val.hora1);
                            $('#horamartes2-editar').val(val.hora2);
                            $('#horamartes3-editar').val(val.hora3);
                            $('#horamartes4-editar').val(val.hora4);

                            if(val.segunda_hora == 1){
                                $('#cbmartessegunda-editar').prop('checked', true);
                            }

                            if(val.cerrado == 1){
                                $('#cbcerradomartes-editar').prop('checked', true);
                            }
                        }
                        else if(val.dia == 4){
                            $('#horamiercoles1-editar').val(val.hora1);
                            $('#horamiercoles2-editar').val(val.hora2);
                            $('#horamiercoles3-editar').val(val.hora3);
                            $('#horamiercoles4-editar').val(val.hora4);

                            if(val.segunda_hora == 1){
                                $('#cbmiercolessegunda-editar').prop('checked', true);
                            }

                            if(val.cerrado == 1){
                                $('#cbcerradomiercoles-editar').prop('checked', true);
                            }
                        }
                        else if(val.dia == 5){
                            $('#horajueves1-editar').val(val.hora1);
                            $('#horajueves2-editar').val(val.hora2);
                            $('#horajueves3-editar').val(val.hora3);
                            $('#horajueves4-editar').val(val.hora4);

                            if(val.segunda_hora == 1){
                                $('#cbjuevessegunda-editar').prop('checked', true);
                            }

                            if(val.cerrado == 1){
                                $('#cbcerradojueves-editar').prop('checked', true);
                            }
                        }
                        else if(val.dia == 6){
                            $('#horaviernes1-editar').val(val.hora1);
                            $('#horaviernes2-editar').val(val.hora2);
                            $('#horaviernes3-editar').val(val.hora3);
                            $('#horaviernes4-editar').val(val.hora4);

                            if(val.segunda_hora == 1){
                                $('#cbviernessegunda-editar').prop('checked', true);
                            }

                            if(val.cerrado == 1){
                                $('#cbcerradoviernes-editar').prop('checked', true);
                            }
                        }
                        else if(val.dia == 7){
                            $('#horasabado1-editar').val(val.hora1);
                            $('#horasabado2-editar').val(val.hora2);
                            $('#horasabado3-editar').val(val.hora3);
                            $('#horasabado4-editar').val(val.hora4);

                            if(val.segunda_hora == 1){
                                $('#cbsabadosegunda-editar').prop('checked', true);
                            }

                            if(val.cerrado == 1){
                                $('#cbcerradosabado-editar').prop('checked', true);
                            }
                        }
                    
                    });              

                }else{
                    toastr.error('Tipo servicio no encontrado');
                }
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle);
                toastr.error('Error del servidor');
        }); 
    }

    // ditar las horas del servicio
    function editarHoras(){
        var id = document.getElementById('id-editar').value;

        var horalunes1 = document.getElementById('horalunes1-editar').value;
        var horalunes2 = document.getElementById('horalunes2-editar').value;
        var horalunes3 = document.getElementById('horalunes3-editar').value;
        var horalunes4 = document.getElementById('horalunes4-editar').value;
        var cblunessegunda = document.getElementById('cblunessegunda-editar').checked;
        var cbcerradolunes = document.getElementById('cbcerradolunes-editar').checked;

        var horamartes1 = document.getElementById('horamartes1-editar').value;
        var horamartes2 = document.getElementById('horamartes2-editar').value;
        var horamartes3 = document.getElementById('horamartes3-editar').value;
        var horamartes4 = document.getElementById('horamartes4-editar').value;
        var cbmartessegunda = document.getElementById('cbmartessegunda-editar').checked;
        var cbcerradomartes = document.getElementById('cbcerradomartes-editar').checked;

        var horamiercoles1 = document.getElementById('horamiercoles1-editar').value;
        var horamiercoles2 = document.getElementById('horamiercoles2-editar').value;
        var horamiercoles3 = document.getElementById('horamiercoles3-editar').value;
        var horamiercoles4 = document.getElementById('horamiercoles4-editar').value;
        var cbmiercolessegunda = document.getElementById('cbmiercolessegunda-editar').checked;
        var cbcerradomiercoles = document.getElementById('cbcerradomiercoles-editar').checked;
        
        var horajueves1 = document.getElementById('horajueves1-editar').value;
        var horajueves2 = document.getElementById('horajueves2-editar').value;
        var horajueves3 = document.getElementById('horajueves3-editar').value;
        var horajueves4 = document.getElementById('horajueves4-editar').value;
        var cbjuevessegunda = document.getElementById('cbjuevessegunda-editar').checked; 
        var cbcerradojueves = document.getElementById('cbcerradojueves-editar').checked;

        var horaviernes1 = document.getElementById('horaviernes1-editar').value;
        var horaviernes2 = document.getElementById('horaviernes2-editar').value;
        var horaviernes3 = document.getElementById('horaviernes3-editar').value;
        var horaviernes4 = document.getElementById('horaviernes4-editar').value;
        var cbviernessegunda = document.getElementById('cbviernessegunda-editar').checked;
        var cbcerradoviernes = document.getElementById('cbcerradoviernes-editar').checked;

        var horasabado1 = document.getElementById('horasabado1-editar').value;
        var horasabado2 = document.getElementById('horasabado2-editar').value;
        var horasabado3 = document.getElementById('horasabado3-editar').value;
        var horasabado4 = document.getElementById('horasabado4-editar').value;
        var cbsabadosegunda = document.getElementById('cbsabadosegunda-editar').checked;
        var cbcerradosabado = document.getElementById('cbcerradosabado-editar').checked;

        var horadomingo1 = document.getElementById('horadomingo1-editar').value;
        var horadomingo2 = document.getElementById('horadomingo2-editar').value;
        var horadomingo3 = document.getElementById('horadomingo3-editar').value;
        var horadomingo4 = document.getElementById('horadomingo4-editar').value;
        var cbdomingosegunda = document.getElementById('cbdomingosegunda-editar').checked;
        var cbcerradodomingo = document.getElementById('cbcerradodomingo-editar').checked;

        var retorno2 = validacionNuevo2(horalunes1, horalunes2, horalunes3, horalunes4, horamartes1, horamartes2, horamartes3, horamartes4,
         horamiercoles1, horamiercoles2, horamiercoles3, horamiercoles4, horajueves1, horajueves2, horajueves3, horajueves4, horaviernes1, horaviernes2, horaviernes3, horaviernes4,
         horasabado1, horasabado2, horasabado3, horasabado4, horadomingo1, horadomingo2, horadomingo3, horadomingo4);

        if(retorno2){
            var cblunessegunda_1 = 0;           
            var cbcerradolunes_1 = 0;
            var cbmartessegunda_1 = 0;           
            var cbcerradomartes_1 = 0;
            var cbmiercolessegunda_1 = 0;           
            var cbcerradomiercoles_1 = 0;
            var cbjuevessegunda_1 = 0;           
            var cbcerradojueves_1 = 0;
            var cbviernessegunda_1 = 0;           
            var cbcerradoviernes_1 = 0;
            var cbsabadosegunda_1 = 0;           
            var cbcerradosabado_1 = 0;
            var cbdomingosegunda_1 = 0;           
            var cbcerradodomingo_1 = 0;

            //--

            if(cblunessegunda){
                cblunessegunda_1 = 1;
            }

            if(cbcerradolunes){
                cbcerradolunes_1 = 1;
            }

            if(cbmartessegunda){
                cbmartessegunda_1 = 1;
            }

            if(cbcerradomartes){
                cbcerradomartes_1 = 1;
            }

            if(cbmiercolessegunda){
                cbmiercolessegunda_1 = 1;
            }

            if(cbcerradomiercoles){
                cbcerradomiercoles_1 = 1;
            }

            if(cbjuevessegunda){
                cbjuevessegunda_1 = 1;
            }

            if(cbcerradojueves){
                cbcerradojueves_1 = 1;
            }

            if(cbviernessegunda){
                cbviernessegunda_1 = 1;
            }

            if(cbcerradoviernes){
                cbcerradoviernes_1 = 1;
            }

            if(cbsabadosegunda){
                cbsabadosegunda_1 = 1;
            }

            if(cbcerradosabado){
                cbcerradosabado_1 = 1;
            }

            if(cbdomingosegunda){
                cbdomingosegunda_1 = 1;
            }

            if(cbcerradodomingo){
                cbcerradodomingo_1 = 1;
            }

            var spinHandle = loadingOverlay().activate();             
            var formData = new FormData();
           
            formData.append('id', id);
            formData.append('horalunes1', horalunes1);
            formData.append('horalunes2', horalunes2);
            formData.append('horalunes3', horalunes3);
            formData.append('horalunes4', horalunes4);
            formData.append('cblunessegunda', cblunessegunda_1);
            formData.append('cbcerradolunes', cbcerradolunes_1);

            formData.append('horamartes1', horamartes1);
            formData.append('horamartes2', horamartes2);
            formData.append('horamartes3', horamartes3);
            formData.append('horamartes4', horamartes4);
            formData.append('cbmartessegunda', cbmartessegunda_1);
            formData.append('cbcerradomartes', cbcerradomartes_1);

            //---

            formData.append('horamiercoles1', horamiercoles1);
            formData.append('horamiercoles2', horamiercoles2);
            formData.append('horamiercoles3', horamiercoles3);
            formData.append('horamiercoles4', horamiercoles4);
            formData.append('cbmiercolessegunda', cbmiercolessegunda_1);
            formData.append('cbcerradomiercoles', cbcerradomiercoles_1);

            //-- 

            formData.append('horajueves1', horajueves1);
            formData.append('horajueves2', horajueves2);
            formData.append('horajueves3', horajueves3);
            formData.append('horajueves4', horajueves4);
            formData.append('cbjuevessegunda', cbjuevessegunda_1);
            formData.append('cbcerradojueves', cbcerradojueves_1);

            formData.append('horaviernes1', horaviernes1);
            formData.append('horaviernes2', horaviernes2);
            formData.append('horaviernes3', horaviernes3);
            formData.append('horaviernes4', horaviernes4);
            formData.append('cbviernessegunda', cbviernessegunda_1);
            formData.append('cbcerradoviernes', cbcerradoviernes_1);

            //---

            formData.append('horasabado1', horasabado1);
            formData.append('horasabado2', horasabado2);
            formData.append('horasabado3', horasabado3);
            formData.append('horasabado4', horasabado4);
            formData.append('cbsabadosegunda', cbsabadosegunda_1);
            formData.append('cbcerradosabado', cbcerradosabado_1);

            formData.append('horadomingo1', horadomingo1);
            formData.append('horadomingo2', horadomingo2);
            formData.append('horadomingo3', horadomingo3);
            formData.append('horadomingo4', horadomingo4);
            formData.append('cbdomingosegunda', cbdomingosegunda_1);
            formData.append('cbcerradodomingo', cbcerradodomingo_1);

            axios.post('/admin/servicios/editar-horas', formData, {
            })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);

                respuesEditarHorario(response);        
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle);
                toastr.error('Error');
            });
        }
    }

    function respuesEditarHorario(response){

        if (response.data.success == 0) { 
            toastr.error('Validacion incorrecta');
        } else if (response.data.success == 1) {
            toastr.success('Actualizado'); 
            $('#modalHorario').modal('hide');
        } else if (response.data.success == 2) {
            toastr.error('Error al actualizar');
        }
        else {
            toastr.error('Error desconocido');
        }        
    }

    function verMapa(id){
        window.location.href="{{ url('/admin/servicios/mapa/ubicacion') }}/"+id;
    }

    function verCategorias(id){
        window.location.href="{{ url('/admin/categorias/') }}/"+id;
    }

    

</script>
 
@endsection