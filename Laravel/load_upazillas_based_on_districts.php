blade:

<div class="col-md-2">
    <div class="form-group">
        <label class="form-label" for="district">District</label>
        <select class="form-select" id="district" name="district" value="{{ old('district') }}" required>
        <option selected disabled value>Choose districts</option>
        @foreach($districts as $district)
        <option value="{{$district->id}}">{{$district->name}}</option>
        @endforeach
        </select>
    </div>
</div>
<div class="col-md-2">
    <div class="form-group">
        <label class="form-label" for="upazila">Upazila</label>
        <select class="form-select" id="upazila" name="upazila" value="{{ old('upazila') }}" required>
        <option selected disabled value>Choose upazila</option>
        </select>
    </div>
</div>



<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#district').change(function() {
        var district_id = $(this).val();
        $.ajax({        
        method:'POST',
        url:"/getupazillasList/"+district_id,
        data: {
            "_token": "{{ csrf_token() }}",
            district_id: district_id
        },
        
        success: function (data) {
            $( "#upazila" ).html(data.html);
        }
        });
    });
</script>

--------------------------------------------------------------------------------------------------

Route:

Route::post('getupazillasList/{id}', [\App\Http\Controllers\Member\MemberController::class, 'upazillaList']);


---------------------------------------------------------------------------------------------------
Controller:

public function upazillaList($district_id) {

    $upazillas = Upazilla::where('district_id',$district_id)->select('id','name')->orderby('name')->get();
    $html = '';
    foreach ( $upazillas as $upazilla ) {
        $html .= '<option value="' . $upazilla->id . '">' . $upazilla->name . '</option>';
    }
    return response()->json(['html' => $html]);
}

---------------------------------------------------------------------------------------------------------------------

Migrations:

Schema::create('districts', function (Blueprint $table) {
    $table->id();

    $table->string('name',25);

    $table->string('status')->default('active');
    $table->string('api_ver',8)->nullable();
    $table->string('app_ver',8)->nullable();
    $table->string('u_agent')->nullable();
    $table->string('ipAddress')->nullable();
    $table->bigInteger('entryby')->unsigned()->nullable();
    $table->foreign('entryby')->references('id')->on('users');
    $table->tinyInteger('active')->default(1);
    $table->timestamps();
    $table->softDeletes();
});


Schema::create('upazillas', function (Blueprint $table) {
    $table->id();

    $table->bigInteger('district_id')->unsigned();
    $table->foreign('district_id')->references('id')->on('districts');
    $table->string('name',25);

    $table->string('status')->default('active');
    $table->string('api_ver',8)->nullable();
    $table->string('app_ver',8)->nullable();
    $table->string('u_agent')->nullable();
    $table->string('ipAddress')->nullable();
    $table->bigInteger('entryby')->unsigned()->nullable();
    $table->foreign('entryby')->references('id')->on('users');
    $table->tinyInteger('active')->default(1);
    $table->timestamps();
    $table->softDeletes();
});

-----------------------------------------------------------
