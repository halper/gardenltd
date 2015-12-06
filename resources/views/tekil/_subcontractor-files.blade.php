{!! Form::model($subcontractor, [
                'url' => "/tekil/$site->slug/save-subcontractor-files",
                'method' => 'POST',
                'class' => 'dropzone',
                'id' => 'file-insert-form',
                'role' => 'form',
                'files'=>true
                ]) !!}
{!! Form::hidden('sub_id', $subcontractor->id) !!}
<div class="fallback">
    <input name="file" type="file" multiple/>
</div>
<div class="dropzone-previews"></div>
<h4 style="text-align: center;color:#428bca;">İlgili belgeleri bu alana
    sürükleyin
    <br>Ya da tıklayın<span
            class="glyphicon glyphicon-hand-down"></span></h4>


{!! Form::close() !!}

@if(!is_null($subcontractor->photo))
    <div class="row">
        <div class="col-sm-12">
            <h4>Kayıtlı Belgeler</h4>
        </div>
        @foreach($subcontractor->photo as $photo)
            <?php
            $my_path_arr = explode(DIRECTORY_SEPARATOR, $photo->file->first()->path);
            $my_path = "/uploads/" . $my_path_arr[sizeof($my_path_arr) - 1];
            if (strpos($photo->file->first()->name, 'pdf') !== false) {
                $image = URL::to('/') . "/img/pdf.jpg";
            } elseif (strpos($photo->file->first()->name, 'doc') !== false) {
                $image = URL::to('/') . "/img/word.png";
            } else {
                $image = URL::to('/') . $my_path . DIRECTORY_SEPARATOR . $photo->file->first()->name;
            }
            ?>

            <a id="lb-link-{{$photo->id}}" href="{{$image}}"
               data-toggle="lightbox" data-gallery="subcontractor-photos"
               data-footer="<a data-dismiss='modal' class='remove-files' href='#' onclick='removeFiles({{$photo->id}})'>Dosyayı Sil<a/>"
               class="col-sm-4">
                <img src="{{$image}}" class="img-responsive">
                {{$photo->file->first()->name}}
            </a>

        @endforeach
    </div>
@endif