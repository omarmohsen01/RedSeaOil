<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl sm:px-6 lg:px-8">
                <form class="row g-3" method="POST" action="{{route('users.store')}}">
                    @csrf
                    <div class="col-md-6" style="margin-bottom: 35px">
                      <x-forms.input label="Name" name="name" placeholder="User Name" />
                    </div>
                    <div class="row" style="margin-bottom: 35px">
                        <div class="col-md-6">
                            <x-forms.input label="Email" name="email" placeholder="User Email" />
                        </div>
                        <div class="col-md-6">
                            <x-forms.input label="Password" type="password" name="password" placeholder="Password" />
                        </div>
                    </div>
                    @if (Auth::user()->type=='SUPER_ADMIN')
                        <fieldset class="row mb-3" style="margin-bottom: 35px">
                            <x-forms.radio name="type"
                            :options="['SUPER_ADMIN'=>'Super Admin','ADMIN'=>'Admin','USER'=>'User']" />
                        </fieldset>
                    @elseif (Auth::user()->type=='ADMIN')
                        <fieldset class="row mb-3" style="margin-bottom: 35px">
                            <x-forms.radio name="type"
                            :options="['ADMIN'=>'Admin','USER'=>'User']" />
                        </fieldset>
                    @endif


                    <div class="form-group col-12">
                      <button type="submit" class="btn btn-outline-success">Save</button>
                    </div>
                  </form>
            </div>
        </div>
    </div>
    {{-- <div class="container">
        <div class="text">
           Contact us Form
        </div>
        <form action="#">
           <div class="form-row">
              <div class="input-data">
                 <input type="text" required>
                 <div class="underline"></div>
                 <label for="">First Name</label>
              </div>
              <div class="input-data">
                 <input type="text" required>
                 <div class="underline"></div>
                 <label for="">Last Name</label>
              </div>
           </div>
           <div class="form-row">
              <div class="input-data">
                 <input type="text" required>
                 <div class="underline"></div>
                 <label for="">Email Address</label>
              </div>
              <div class="input-data">
                 <input type="text" required>
                 <div class="underline"></div>
                 <label for="">Website Name</label>
              </div>
           </div>
           <div class="form-row">
           <div class="input-data textarea">
              <textarea rows="8" cols="80" required></textarea>
              <br />
              <div class="underline"></div>
              <label for="">Write your message</label>
              <br />
              <div class="form-row submit-btn">
                 <div class="input-data">
                    <div class="inner"></div>
                    <input type="submit" value="submit">
                 </div>
              </div>
        </form>
        </div>
    </div> --}}
</x-app-layout>
