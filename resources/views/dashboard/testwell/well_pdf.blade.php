<!DOCTYPE html>
<html>

<head>
    <title>Red Sea Oil</title>
    <link rel="stylesheet" href="{{ asset('build/assets/wellPDF.css') }}">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
    @foreach ($options as $option)
        <div class="option">
            <div class="card">
                <div class="card head" style="border: 1px solid">
                    <div class="row g-0">
                        <div class="col-md-4 ">
                            <img src={{ asset('storage/Capture.PNG') }} class="icon img-fluid  p-2">
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <h3><b>{{ $well->name }}</b></h1>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h4><b>From: {{ $well->from }}</b>
                                </h3>
                            </div>
                            <div class="col-md-6">
                                <h4><b>To: {{ $well->to }}</b></h3>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Well: {{ $well->well }}</h3>
                            </div>
                            <div class="col-md-6">
                                <h4>Rig: {{ $well->rig }}</h3>
                            </div>
                        </div>
                        <h4>Gauge Installed/Pulled By:{{ $well->user->name }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class=" structure_desciption">
            @foreach ($option->structures as $structure)
                <h3 class="strcuctureName text-center mb-0  mt-2"><b>{{ $structure->name }}</b></h3>
                <div class="grid ">
                    @foreach ($structureDescriptions as $strc_desc)
                        @if ($structure->id == $strc_desc->structure_id)
                            <div class="tableRows">
                                <span style="text-align: left;"><b>{{ $strc_desc->input }}:</b></span>
                                <span style="text-align: left;">
                                    @php
                                        $data = $strc_desc->pivot->data;
                                        $decodedData = json_decode($data, true);

                                        if (is_array($decodedData)) {
                                            $numericValues = array_filter($decodedData, 'is_numeric');
                                            $formattedData = implode(' ', $numericValues);
                                        } else {
                                            $formattedData = trim($data, '"');
                                        }
                                    @endphp
                                    <b>{{ $formattedData }}</b>
                                </span>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endforeach
        </div>
        </div>
        </div>
    @endforeach
</body>

</html>
