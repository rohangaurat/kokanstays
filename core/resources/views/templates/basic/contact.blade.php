@extends('Template::layouts.frontend')

@php 
    $contactContent = getContent('contact_us.content', true); 
    $user = auth()->user(); 
@endphp

@section('content')
<div class="contact-section py-80">
    <div class="container">

        <div class="contact-top mb-5">
            <div class="row gy-4 justify-content-center">

                <div class="col-lg-4 col-md-6">
                    <div class="modern-card">
                        <div class="contact-item__icon address-icon">
                            @php echo $contactContent?->data_values?->address_icon ?? '<i class="las la-map-marker"></i>'; @endphp
                        </div>
                        <div class="contact-item__content">
                            <h6 class="contact-item__title">
                                {{ __($contactContent?->data_values?->address_title ?? 'Our Location') }}
                            </h6>
                            <p class="contact-item__desc">
                                {{ __($contactContent?->data_values?->address ?? 'Location not set') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="modern-card">
                        <div class="contact-item__icon email-icon">
                            @php echo $contactContent?->data_values?->email_icon ?? '<i class="las la-envelope"></i>'; @endphp
                        </div>
                        <div class="contact-item__content">
                            <h6 class="contact-item__title">
                                {{ __($contactContent?->data_values?->email_title ?? 'Email Us') }}
                            </h6>
                            <a href="mailto:{{ $contactContent?->data_values?->email ?? '' }}" class="contact-item__desc">
                                {{ $contactContent?->data_values?->email ?? 'support@example.com' }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="modern-card">
                        <div class="contact-item__icon whatsapp-icon">
                            <i class="lab la-whatsapp"></i>
                        </div>
                        <div class="contact-item__content">
                            <h6 class="contact-item__title">WhatsApp</h6>
                            @php 
                                $phone = $contactContent?->data_values?->phone ?? '';
                                $cleanPhone = preg_replace('/[^0-9]/', '', $phone); 
                                $waMsg = urlencode('Hello KokanStays, I would like to inquire about booking.');
                            @endphp
                            <a href="https://wa.me/{{ $cleanPhone }}?text={{ $waMsg }}" target="_blank" class="contact-item__desc">
                                {{ $phone ?: '+91 0000000000' }}
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="contact-bottom mt-5">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-xl-6">
                    <div class="modern-form">
                        <div class="text-center mb-4">
                            <h3 class="contact-form__title">
                                {{ __($contactContent?->data_values?->heading ?? 'Get In Touch') }}
                            </h3>
                            <p class="text-muted">Fill out the form below and we'll get back to you shortly.</p>
                        </div>

                        <form method="POST" action="" class="verify-gcaptcha disableSubmission">
                            @csrf
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">Your Name</label>
                                    <input type="text" name="name" class="form--control modern-input"
                                           placeholder="Full Name" value="{{ old('name', $user?->fullname) }}" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">Your Email</label>
                                    <input type="email" name="email" class="form--control modern-input"
                                           placeholder="email@example.com" value="{{ old('email', $user?->email) }}" 
                                           @readonly($user && $user->profile_complete) required>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold small">Subject</label>
                                    <input type="text" name="subject" class="form--control modern-input"
                                           placeholder="What is this regarding?" value="{{ old('subject') }}" required>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold small">Message</label>
                                    <textarea name="message" rows="5" class="form--control modern-input"
                                              placeholder="Tell us how we can help..." required>{{ old('message') }}</textarea>
                                </div>

                                <div class="col-12">
                                    <x-captcha />
                                </div>

                                <div class="col-12 text-center">
                                    <button type="submit" class="btn modern-btn w-100 py-3">
                                        Send Message <span class="ms-2">🚀</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('style')
<style>
    /* ✅ PERFECT CENTERING UTILITY */
    .modern-card {
        padding: 40px 25px;
        border-radius: 24px;
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.06);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        display: flex;
        flex-direction: column;
        align-items: center;    /* Center horizontally */
        justify-content: center; /* Center vertically */
        text-align: center;
        height: 100%;
    }

    .modern-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
        border-color: #0d6efd;
    }

    /* ✅ ICON FIX */
    .contact-item__icon {
        width: 70px;
        height: 70px;
        border-radius: 20px;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        margin-bottom: 20px;
        font-size: 30px;
        position: relative;
    }

    /* Target nested icons specifically */
    .contact-item__icon i, 
    .contact-item__icon svg {
        display: block;
        line-height: 1;
        margin: 0 auto;
    }

    .address-icon { background: #fff9e6; color: #ffc107; }
    .email-icon { background: #eef4ff; color: #0d6efd; }
    .whatsapp-icon { background: #e9fbf0; color: #25D366; }

    /* ✅ TEXT CENTERING */
    .contact-item__content {
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .contact-item__title {
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: 10px;
        color: #1a1a1a;
    }

    .contact-item__desc {
        color: #6c757d;
        text-decoration: none;
        font-size: 0.95rem;
        line-height: 1.6;
        transition: 0.3s;
        word-break: break-word;
    }

    .contact-item__desc:hover {
        color: #0d6efd;
    }

    /* ✅ FORM DESIGN */
    .modern-form {
        padding: 50px;
        border-radius: 30px;
        background: #ffffff;
        box-shadow: 0 15px 50px rgba(0, 0, 0, 0.05);
        border: 1px solid #f1f1f1;
    }

    .modern-input {
        background-color: #f8f9fa;
        border: 2px solid transparent;
        padding: 15px 20px;
        border-radius: 12px;
        font-size: 15px;
        transition: 0.3s;
    }

    .modern-input:focus {
        background-color: #ffffff;
        border-color: #0d6efd;
        box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
        outline: none;
    }

    .modern-btn {
        background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
        color: #fff;
        font-weight: 600;
        border-radius: 12px;
        border: none;
        transition: 0.3s;
    }

    .modern-btn:hover {
        transform: scale(1.02);
        box-shadow: 0 10px 20px rgba(13, 110, 253, 0.2);
    }

    @media (max-width: 768px) {
        .modern-form { padding: 30px 20px; }
    }
</style>
@endpush