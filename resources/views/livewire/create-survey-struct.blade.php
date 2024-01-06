<div>
    <x-alert />
    <form class="row g-3" method="POST" action="{{ route('surveys.store') }}" style="width: 1200px;">
        @csrf
        <div class="col-md-6" style="margin-bottom: 35px">
            <x-forms.input label="Survey Name" name="surveyName" placeholder="Survey Name" />
        </div>
        <div class="card-body">
            <div class="col-md-6" style="margin-bottom: 35px">
                <div style="margin-bottom: 10px">
                    <x-forms.input label="Structure Name" name="structureName" placeholder="Structure Name" />
                </div>

                @foreach ($structuresDes as $index => $stuctureDesc)
                    <div class="input-group mb-3" style="margin-left: 10px">

                        <x-forms.input type="text" name="structuresDes[{{ $index }}][input]"
                            wire='wire:model="structuresDes.{{ $index }}.input"' placeholder="Input" />

                        <select name="structuresDes[{{ $index }}][type]"
                            wire:model="structuresDes.{{ $index }}.type" class="form-control">
                            @foreach ($types as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>

                        <fieldset class="row ">
                            <x-forms.radio name="type" :options="['Required' => 'Required', 'Optional' => 'Optional']"
                                name="structuresDes[{{ $index }}][is_require]"
                                wire:model="structuresDes.{{ $index }}.is_require" />
                        </fieldset>

                        <a href="#" class="btn btn-outline-danger"
                            wire:click.prevent="removeStruDesc({{ $index }})"
                            style="padding: 12px;margin-left: -95px;">Delete</a>
                    </div>

{{--                    <div class="flex items-center mb-4">--}}
{{--                        <input id="banner" type="checkbox" value="" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">--}}
{{--                        <label for="banner" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Banner</label>--}}
{{--                    </div>--}}
                @endforeach

                @foreach ($structuresDesMenu as $index => $structureDesMenu)
                    <div class="input-group mb-3" style="margin-left: 10px">
                        <x-forms.input type="hidden" name="structuresDesMenu[{{ $index }}][type]" value="List" />
                        <div>
                            <input type="text" wire:model="structuresDesMenu.{{ $index }}.name"
                                name="structuresDesMenu[{{ $index }}][input]" placeholder="Input" />
                            <fieldset class="row ">
                                <x-forms.radio name="type" :options="['Required' => 'Required', 'Optional' => 'Optional']"
                                    name="structuresDesMenu[{{ $index }}][is_require]"
                                    wire:model="structuresDesMenu.{{ $index }}.is_require" />
                            </fieldset>
                            @foreach ($structureDesMenu['data'] as $dataIndex => $data)
                                <div class="form-group" style="margin: 5px">
                                    <input type="text"
                                        wire:model="structuresDesMenu.{{ $index }}.data.{{ $dataIndex }}"
                                        name="structuresDesMenu[{{ $index }}][data][{{ $dataIndex }}]"
                                        placeholder="Data" />
                                    <a href=""
                                        wire:click.prevent="removeData({{ $index }}, {{ $dataIndex }})"><span class="material-symbols-outlined">delete</span></a>
                                </div>
                            @endforeach
                            <button class="btn btn-outline-secondary" wire:click.prevent="addData({{ $index }})">Add
                                Data</button>
                            <button class="btn btn-outline-danger"
                                wire:click.prevent="removeStruMenu({{ $index }})">Remove Description</button>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="btn-group">
                    <button class="btn btn-sm btn-outline-secondary" wire:click.prevent="addStruDesc">+ Add Description</button>
                    <button class="btn btn-outline-primary" wire:click.prevent="addStruMenu">+Add In Menu</button>
                    <button style="margin-left: 5px" type="submit" class="btn btn-outline-success">Save</button>
            </div>
        </div>
</div>
</form>
</div>
