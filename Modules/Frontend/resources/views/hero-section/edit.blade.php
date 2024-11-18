@extends('admin.master_layout')
@section('title')
    <title>{{ __('Hero Section') }}</title>
@endsection
@section('admin-content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ __('Hero Section') }}</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a>
                    </div>
                    <div class="breadcrumb-item">{{ __('Dashboard') }}</div>
                </div>
            </div>
            <div class="section-body row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="service_card">{{ __('Available Translations') }}</h5>

                            <hr>
                            @if ($code !== $languages->first()->code)
                                <button onclick="translateAll()" class="btn btn-primary"
                                    id="translate-btn">{{ __('Translate') }}</button>
                            @endif

                        </div>
                        <div class="card-body">
                            <div class="lang_list_top">
                                <ul class="lang_list">
                                    @foreach ($languages as $language)
                                        <li><a id="{{ request('code') == $language->code ? 'selected-language' : '' }}"
                                                href="{{ route('admin.hero-section.edit', ['hero_section' => 1, 'code' => $language->code]) }}"><i
                                                    class="fas {{ request('code') == $language->code ? 'fa-eye' : 'fa-edit' }}"></i>
                                                {{ $language->name }}</a></li>
                                    @endforeach
                                </ul>
                            </div>

                            <div class="mt-2 alert alert-danger" role="alert">
                                @php
                                    $current_language = $languages->where('code', request()->get('code'))->first();
                                @endphp
                                <p>{{ __('Your editing mode') }} :
                                    <b>{{ $current_language?->name }}</b>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="section-body">
                <div class="mt-4 row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4>{{ __('Update Hero Section') }}</h4>
                                <div>
                                    <a href="{{ route('admin.blog-category.index') }}" class="btn btn-primary"><i
                                            class="fa fa-arrow-left"></i>{{ __('Back') }}</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <form
                                    action="{{ route('admin.hero-section.update', ['hero_section' => $heroSection->id, 'code' => $code]) }}"
                                    method="post" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="row">
                                        @if (Cache::get('setting')?->site_theme == 'theme-three')
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="sort_title">{{ __('Short Title') }}</label>
                                                    <input data-translate="true" type="text" id="sort_title"
                                                        name="sort_title"
                                                        value="{{ $heroSection->getTranslation($code)?->sort_title }}"
                                                        placeholder="{{ __('Enter Subtitle') }}" class="form-control">
                                                    @error('sort_title')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        @endif
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="title">{{ __('Title') }}<span
                                                        class="text-danger">*</span></label>
                                                <input data-translate="true" type="text" id="title" name="title"
                                                    value="{{ $heroSection->getTranslation($code)?->title }}"
                                                    placeholder="{{ __('Enter Title') }}" class="form-control">
                                                <small>{{ __('wrap your word with [] for highlight and \ for break and {} for bold') }}</small>
                                                @error('title')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="sub_title">{{ __('Sub Title') }}<span class="text-danger">
                                                        ({{ __('leave blank for hide') }})</span></label>
                                                <input data-translate="true" type="text" id="sub_title" name="sub_title"
                                                    value="{{ $heroSection->getTranslation($code)?->sub_title }}"
                                                    placeholder="{{ __('Enter Subtitle') }}" class="form-control">
                                                <small>{{ __('use \ for break') }}</small>
                                                @error('sub_title')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="action_button_text">{{ __('Action button text') }}<span
                                                        class="text-danger">
                                                        ({{ __('leave blank for hide') }})</span></label>
                                                <input data-translate="true" type="text" id="action_button_text"
                                                    name="action_button_text"
                                                    value="{{ $heroSection->getTranslation($code)?->action_button_text }}"
                                                    placeholder="{{ __('Action Button Text') }}" class="form-control">
                                                @error('action_button_text')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 {{ $code == $languages->first()->code ? '' : 'd-none' }}">
                                            <div class="form-group">
                                                <label for="action_button_url">{{ __('Action button url') }}</label>
                                                <input data-translate="false" type="text" id="action_button_url"
                                                    name="action_button_url" value="{{ $heroSection->action_button_url }}"
                                                    placeholder="{{ __('Action Button Url') }}" class="form-control">
                                                @error('action_button_url')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label data-translate="true"
                                                    for="video_button_text">{{ __('Video button text') }}<span
                                                        class="text-danger">
                                                        ({{ __('leave blank for hide') }})</span></label>
                                                <input data-translate="true" type="text" id="video_button_text"
                                                    name="video_button_text"
                                                    value="{{ $heroSection->getTranslation($code)?->video_button_text }}"
                                                    placeholder="{{ __('Video Button Text') }}" class="form-control">
                                                <small>{{ __('use \ for break') }}</small>
                                                @error('video_button_text')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6 {{ $code == $languages->first()->code ? '' : 'd-none' }}">
                                            <div class="form-group">
                                                <label for="video_button_url">{{ __('Video button url') }}</label>
                                                <input data-translate="false" type="text" id="video_button_url"
                                                    name="video_button_url" value="{{ $heroSection->video_button_url }}"
                                                    placeholder="{{ __('Video Button Url') }}" class="form-control">
                                                @error('video_button_url')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="total_students">{{ __('Total Students') }} <span
                                                        class="text-danger">
                                                        ({{ __('leave blank for hide') }})</span></label>
                                                <input data-translate="true" type="text" id="total_students"
                                                    name="total_student"
                                                    value="{{ $heroSection->getTranslation($code)?->total_student }}"
                                                    placeholder="{{ __('10k') }}" class="form-control">
                                                @error('total_student')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="total_instructors">{{ __('Total Instructors') }} <span
                                                        class="text-danger">
                                                        ({{ __('leave blank for hide') }})</span></label>
                                                <input data-translate="true" type="text" id="total_instructors"
                                                    name="total_instructor"
                                                    value="{{ $heroSection->getTranslation($code)?->total_instructor }}"
                                                    placeholder="{{ __('10k') }}" class="form-control">
                                                @error('total_instructor')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        @if ($code == $languages->first()->code)
                                            @if (!in_array($setting?->site_theme, ['theme-four']))
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>{{ __('Banner Image') }}<span
                                                                class="text-danger">*</span></label>
                                                        <div id="image-preview" class="image-preview">
                                                            <label for="image-upload"
                                                                id="image-label">{{ __('Image') }}</label>
                                                            <input data-translate="false" type="file"
                                                                name="banner_image" id="image-upload">
                                                        </div>
                                                        @error('banner_image')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>{{ __('Banner Background') }}<span
                                                                class="text-danger">*</span></label>
                                                        <div id="image-preview-2" class="image-preview">
                                                            <label for="image-upload"
                                                                id="image-label-2">{{ __('Image') }}</label>
                                                            <input data-translate="false" type="file"
                                                                name="banner_background" id="image-upload-2">
                                                        </div>
                                                        @error('banner_background')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>{{ __('Hero Background') }}<span
                                                            class="text-danger">*</span></label>
                                                    <div id="image-preview-3" class="image-preview">
                                                        <label for="image-upload"
                                                            id="image-label-3">{{ __('Image') }}</label>
                                                        <input data-translate="false" type="file"
                                                            name="hero_background" id="image-upload-3">
                                                    </div>
                                                    @error('hero_background')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>{{ __('Enrolled students image') }}<span
                                                            class="text-danger">*</span></label>
                                                    <div id="image-preview-4" class="image-preview">
                                                        <label for="image-upload"
                                                            id="image-label-4">{{ __('Image') }}</label>
                                                        <input data-translate="false" type="file"
                                                            name="enroll_students_image" id="image-upload-4">
                                                    </div>
                                                    @error('enroll_students_image')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        @endif


                                        <div class="text-center col">
                                            <x-admin.save-button :text="__('Save')">
                                            </x-admin.save-button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('js')
    <script src="{{ asset('backend/js/jquery.uploadPreview.min.js') }}"></script>
    <script>
        var banner_image = "url('{{ asset($heroSection->banner_image) }}')";
        var banner_background = "url('{{ asset($heroSection->banner_background) }}')";
        var hero_background = "url('{{ asset($heroSection->hero_background) }}')";
        @if ($setting?->site_theme == 'theme-one')
            banner_image =
                "url('{{ manageAssetImage('uploads/homepages/main/banner_img.png', $heroSection->banner_image) }}')";
            banner_background =
                "url('{{ manageAssetImage('uploads/homepages/main/banner_bg.png', $heroSection->banner_background) }}')";
            hero_background =
                "url('{{ manageAssetImage('uploads/homepages/main/hero_bg.png', $heroSection->hero_background) }}')";
        @elseif ($setting?->site_theme == 'theme-two')
            banner_image =
                "url('{{ manageAssetImage('uploads/homepages/online/banner_img.png', $heroSection->banner_image) }}')";
            banner_background =
                "url('{{ manageAssetImage('uploads/homepages/online/banner_bg.svg', $heroSection->banner_background) }}')";
            hero_background =
                "url('{{ manageAssetImage('uploads/homepages/online/hero_bg.png', $heroSection->hero_background) }}')";
        @elseif ($setting?->site_theme == 'theme-three')
            banner_image =
                "url('{{ manageAssetImage('uploads/homepages/university/banner_img.png', $heroSection->banner_image) }}')";
            banner_background =
                "url('{{ manageAssetImage('uploads/homepages/university/banner_bg.svg', $heroSection->banner_background) }}')";
            hero_background =
                "url('{{ manageAssetImage('uploads/homepages/university/hero_bg.jpg', $heroSection->hero_background) }}')";
        @elseif ($setting?->site_theme == 'theme-four')
            hero_background =
                "url('{{ manageAssetImage('uploads/homepages/business/hero_bg.jpg', $heroSection->hero_background) }}')";
        @endif
        @if (!in_array($setting?->site_theme, ['theme-four']))
            $.uploadPreview({
                input_field: "#image-upload",
                preview_box: "#image-preview",
                label_field: "#image-label",
                label_default: "{{ __('Choose Icon') }}",
                label_selected: "{{ __('Change Icon') }}",
                no_label: false,
                success_callback: null
            });
            $.uploadPreview({
                input_field: "#image-upload-2",
                preview_box: "#image-preview-2",
                label_field: "#image-label-2",
                label_default: "{{ __('Choose Icon') }}",
                label_selected: "{{ __('Change Icon') }}",
                no_label: false,
                success_callback: null
            });
            $('#image-preview').css({
                'background-image': banner_image,
                'background-size': 'contain',
                'background-position': 'center',
                'background-repeat': 'no-repeat'
            });
            $('#image-preview-2').css({
                'background-image': banner_background,
                'background-size': 'contain',
                'background-position': 'center',
                'background-repeat': 'no-repeat'
            });
        @endif
        $.uploadPreview({
            input_field: "#image-upload-3",
            preview_box: "#image-preview-3",
            label_field: "#image-label-3",
            label_default: "{{ __('Choose Icon') }}",
            label_selected: "{{ __('Change Icon') }}",
            no_label: false,
            success_callback: null
        });

        $.uploadPreview({
            input_field: "#image-upload-4",
            preview_box: "#image-preview-4",
            label_field: "#image-label-4",
            label_default: "{{ __('Choose Icon') }}",
            label_selected: "{{ __('Change Icon') }}",
            no_label: false,
            success_callback: null
        });

        $('#image-preview-3').css({
            'background-image': hero_background,
            'background-size': 'contain',
            'background-position': 'center',
            'background-repeat': 'no-repeat'
        });

        $('#image-preview-4').css({
            'background-image': 'url({{ asset($heroSection->enroll_students_image) }})',
            'background-size': 'contain',
            'background-position': 'center',
            'background-repeat': 'no-repeat'
        });

        $('#translate-btn').on('click', function() {
            translateAllTo("{{ $code }}");
        })
    </script>
@endpush
