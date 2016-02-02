<h3>Yeni Talep</h3>

<p>Ana malzeme seçiliminden sonra bağlantı malzemeleri yüklenecektir.</p>
<p>1. seviye bağlantı malzeme kutucuğunu, yalın bağlantı malzemeleri için kullanınız.</p>
<p>2. seviye bağlantı yapılacak 1. seviye malzeme kutucuğunu, 1. seviye bağlantı malzemelerine 2. seviye malzeme (özellik) eklenecekse kullanınız.</p>


<div class="form-group">
    <label for="materials">Ana Malzeme: </label>
    <select id="materials" name="materials"
            class="js-example-basic-multiple form-control">
        <?php
        $materials = \App\Material::all()
        ?>
        <option value="" disabled selected>Ana Malzemeyi Seçiniz</option>
        @foreach($materials as $mat)

            <option value="{{$mat->id}}">{{$mat->material}}</option>

        @endforeach
    </select>
</div>
@include('tekil._demand-submaterials')