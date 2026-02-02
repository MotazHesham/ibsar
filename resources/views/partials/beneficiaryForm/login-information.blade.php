<form method="POST" action="{{ route($routeName ?? 'admin.beneficiaries.update', $beneficiary->id) }}"
    enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" name="user_id" value="{{ $user->id }}">
    <input type="hidden" name="step" value="login_information">
    <div class="row justify-content-center">
        <div class="col-xl-8">
            <div class="row gy-3">
                @field('identity_num')
                @include('utilities.form.text', [
                    'name' => 'identity_num',
                    'label' => 'cruds.user.fields.identity_num',
                    'isRequired' => true,
                    'grid' => 'col-md-6',
                    'value' => $user->identity_num ?? '',
                ])
                @endfield
                @field('email')
                @include('utilities.form.text', [
                    'name' => 'email',
                    'label' => 'cruds.user.fields.email',
                    'isRequired' => false,
                    'type' => 'email',
                    'grid' => 'col-md-6',
                    'value' => $user->email ?? '',
                ])
                @endfield
                @field('phone')
                @include('utilities.form.text', [
                    'name' => 'phone',
                    'label' => 'cruds.user.fields.phone',
                    'isRequired' => true,
                    'grid' => 'col-md-6',
                    'value' => $user->phone ?? '',
                ])
                @endfield
                @field('phone_2')
                @include('utilities.form.text', [
                    'name' => 'phone_2',
                    'label' => 'cruds.user.fields.phone_2',
                    'isRequired' => false,
                    'grid' => 'col-md-6',
                    'value' => $user->phone_2 ?? '',
                ])
                @endfield
                @field('password')
                @include('utilities.form.text', [
                    'name' => 'password',
                    'label' => 'cruds.user.fields.password',
                    'isRequired' => false,
                    'type' => 'password',
                    'grid' => 'col-md-6',
                ])
                @endfield
                @field('region_id')
                @include('utilities.form.select', [
                    'name' => 'region_id',
                    'label' => 'cruds.beneficiary.fields.region',
                    'isRequired' => true,
                    'options' => getRegions(),
                    'grid' => 'col-md-6', 
                    'search' => true,
                    'value' => $beneficiary->region_id ?? '',
                ]) 
                @endfield
                @field('beneficiary_category_id')
                    @include('utilities.form.select', [
                        'name' => 'beneficiary_category_id',
                        'label' => 'cruds.beneficiary.fields.beneficiary_category',
                        'isRequired' => \App\Helpers\FieldVisibilityHelper::isFieldRequired('beneficiary_category_id'),
                        'options' => $beneficiary_categories,
                        'grid' => 'col-md-6',
                        'value' => $beneficiary->beneficiary_category_id ?? '',
                    ])
                @endfield
            </div>
        </div>
        <div class="col-xl-4">
            @field('photo')
            @include('utilities.form.photo', [
                'name' => 'photo',
                'id' => 'userPhoto',
                'url' => route($storeMediaUrl ?? 'admin.users.storeMedia'),
                'label' => 'cruds.user.fields.photo',
                'isRequired' => false,
                'model' => $user ?? '',
            ])
            @endfield
        </div>
    </div>
    <button type="submit" class="btn btn-primary mt-3">
        {{ trans('global.update') }}
    </button>
    
    @if(getSetting('auto_accept_beneficiary') == 'yes' && auth()->user()->is_beneficiary && $beneficiary->canRequestOrder())
        <button type="submit" class="btn btn-success mt-3" name="redirect_to" value="request_order">
            {{ trans('cruds.beneficiary.extra.update_and_request_order') }}
        </button>
    @endif
</form>
