<!DOCTYPE html>
<html>
<head>
<title>MAIN MENU | Aplikasi Uji Coba</title>     
</head>

 <div class="container">
        <h1 style="font-size:20pt">DATA MASTER</h1>
        <h4>Daftar Penyewa</h4>
        <br />
        <button class="btn btn-success" onclick="add_Daftar()"><i class="glyphicon glyphicon-plus"></i> Tambah Penyewa</button>
        <button class="btn btn-primary hidden-print" onclick="window.location.href='./cetakDaftar'"><i class="glyphicon glyphicon-print"></i> Cetak</button>
        <button class="btn btn-info" onclick="cr_tgl()"><i class="glyphicon glyphicon-search"></i> Cari Pertanggal</button>
        <button class="btn btn-info" onclick="window.location.href='./Daftar'"><i class="glyphicon glyphicon-refresh"></i> Reload</button>      
        <br />
        <br />
        <table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>kode</th>
                    <th>Nama</th>
                    <th>Jenis Kelamin</th>
                    <th>Tgl</th>
                    <th>Alamat</th>
                    <th style="width:155px;">Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>

            <tfoot>
            <tr>
                    <th>kode</th>
                    <th>Nama</th>
                    <th>Jenis Kelamin</th>
                    <th>Tgl</th>
                    <th>Alamat</th>
                <th>Action</th>
            </tr>
            </tfoot>
        </table>
    </div>



<script type="text/javascript">
var save_method; //for save method string
var table;

$(document).ready(function() {
    //datatables
    table = $('#table').DataTable({ 

        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "order": [], //Initial no order.

        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": "<?php echo site_url('Daftar/Daftar_list')?>",
            "type": "POST"
        },

        //Set column definition initialisation properties.
        "columnDefs": [
        { 
            "targets": [ -1 ], //last column
            "orderable": false, //set not orderable
        },
        ],

    });

    //datepicker
    
    $('.datepicker').datepicker({
        autoclose: true,
        format: "yyyy-mm-dd",
        todayHighlight: true,
        orientation: "top auto",
        todayBtn: true,
        todayHighlight: true,  
    });



    //set input/textarea/select event when change value, remove class error and remove text help block 
    $("input").change(function(){
        $(this).parent().parent().removeClass('has-error');
        $(this).next().empty();
    });
    $("textarea").change(function(){
        $(this).parent().parent().removeClass('has-error');
        $(this).next().empty();
    });
    $("select").change(function(){
        $(this).parent().parent().removeClass('has-error');
        $(this).next().empty();
    });

});



function add_Daftar()
{
    save_method = 'add';
    $('#form')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string
    $('#modal_form').modal('show'); // show bootstrap modal
    $('.modal-title').text('Tambah Data Mahasiswa'); // Set Title to Bootstrap modal title
}

function save()
{
    $('#btnSave').text('saving...'); //change button text
    $('#btnSave').attr('disabled',true); //set button disable 
    var url;
    if(save_method == 'add') {
        url = "<?php echo site_url('Daftar/Daftar_add')?>";
    } else {
        url = "<?php echo site_url('Daftar/Dftar_update')?>";
    }
    // ajax adding data to database
    $.ajax({
        url : url,
        type: "POST",
        data: $('#form').serialize(),
        dataType: "JSON",
        success: function(data)
        {

            if(data.status) //if success close modal and reload ajax table
            {
                $('#modal_form').modal('hide');
                reload_table();
            }
            else
            {
                for (var i = 0; i < data.inputerror.length; i++) 
                {
                    $('[name="'+data.inputerror[i]+'"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
                    $('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]); //select span help-block class set text error string
                }
            }
            $('#btnSave').text('save'); //change button text
            $('#btnSave').attr('disabled',false); //set button enable 


        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error adding / update data');
            $('#btnSave').text('save'); //change button text
            $('#btnSave').attr('disabled',false); //set button enable 

        }
    });
}

function cr_tgl()
{
    find_method = 'cari_tgl';
    $('#formTgl')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string
    $('#modal_form_tgl').modal('show'); // show bootstrap modal
    $('.modal-title').text('Cari Tanggal Lahir'); // Set Title to Bootstrap modal title
}



function edit_Daftar(kode)
{
    save_method = 'update';
    $('#form')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string

    //Ajax Load data from ajax
    $.ajax({
        url : "<?php echo site_url('Daftar/Daftar_edit/')?>/" + kode,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

            $('[name="kode"]').val(data.kode);
            $('[name="nama"]').val(data.nama);
            $('[name="jk"]').val(data.jk);
            $('[name="tgl"]').datepicker('update',data.tgl);
            $('[name="alamat"]').val(data.alamat);    
            $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Edit Mahasiswa'); // Set title to Bootstrap modal title

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Kesalahan dalam mengambil data');
        }
    });
}


function delete_Daftar(kode)
{
    if(confirm('Apakah yakin untuk menghapus data nim : '+kode +' ? '))
    {
        // ajax delete data to database
        $.ajax({
            url : "<?php echo site_url('mhs/mhs_delete')?>/"+kode,
            type: "POST",
            dataType: "JSON",
            success: function(data)
            {
                //if success reload ajax table
                $('#modal_form').modal('hide');
                reload_table();
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error deleting data');
            }
        });

    }
}

function reload_table()
{
    table.ajax.reload(null,false); //reload datatable ajax 
}


</script>

<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Person Form</h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="form" class="form-horizontal">
                    <!--<input type="hidden" value="" name="id"/> -->
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-3">kode</label>
                            <div class="col-md-9">
                                <input name="nim" placeholder="Nim Anda" class="form-control" type="text">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Nama</label>
                            <div class="col-md-9">
                                <input name="nama" placeholder="Nama Lengkap" class="form-control" type="text">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Jenis Kelamin</label>
                            <div class="col-md-9">
                                <select name="jk" class="form-control">
                                    <option value="">-- Pilih Jenis Kelamin --</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Tanggal Lahir</label>
                            <div class="col-md-9">
                                <input name="tgl" placeholder="yyyy-mm-dd" class="form-control datepicker" type="text">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Alamat</label>
                            <div class="col-md-9">
                                <textarea name="alamat" placeholder="Address" class="form-control"></textarea>
                                <span class="help-block"></span>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->


<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form_tgl" >
    <div class="modal-dialog" style="width: 450px; height: 300px; padding-top:30px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Cari Pertanggal</h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="formTgl" class="form-horizontal">
                    <!--<input type="hidden" value="" name="id"/> -->
 
                        <div class="form-group">
                            <label class="control-label col-md-4">Tanggal Awal</label>
                            <div class="col-md-6">
                                <input name="TglAwal" id="TglAwal" placeholder="yyyy-mm-dd" class="form-control datepicker" type="text" required>
                           
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4">Tanggal Akhir</label>
                            <div class="col-md-6">
                                <input name="TglAkhir" id="TglAkhir" placeholder="yyyy-mm-dd" class="form-control datepicker" type="text" required>
                               
                            </div>
                        </div>
                    

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnCari" onclick="carilah()" class="btn btn-primary">Find</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->

</body>
<script>

function carilah()
{
    //$('#btnCari').text('Find Data...'); //change button text
   // $('#btnCari').attr('disabled',true); //set button disable 
    $('#modal_form_tgl').modal('hide'); // show bootstrap modal
    var TglAwal= $("#TglAwal").val();
     var TglAkhir= $("#TglAkhir").val();
    
     table = $('#table').DataTable({ 
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "order": [], //Initial no order.
        destroy: true, //MENGATASI reinitialise !!
        // Load data for the table's content from an Ajax source
        "ajax": {
             "url": "<?php echo site_url('mhs/cari_tanggal/')?>",
            //"url": "<?php echo site_url('riwayat_lansia/detil_riwayat_lansia/')?>",
            "type": "POST",
            data: {TglAwal:TglAwal,TglAkhir:TglAkhir,},             
        },
        //Set column definition initialisation properties.
        "columnDefs": [
        { 
            "targets": [ -1 ], //last column
            "orderable": false, //set not orderable
        },
        ],
    });
}

</script>
</html>