@extends('layouts.auth')

@section('title', 'Register')
@section('heading', 'Create an account')
@section('description', 'Enter your details below to create your account')

@section('content')
<div
    x-data="{
        agreedToTerms: false,
        showTermsModal: false,
        activeTab: 'terms',
        termsError: '',
        submitForm(e) {
            if (!this.agreedToTerms) {
                this.termsError = 'You must agree to the Terms and Conditions and Privacy Policy.';
                e.preventDefault();
                return;
            }
            this.termsError = '';
        }
    }"
>
    <form
        method="POST"
        action="{{ route('register') }}"
        class="flex flex-col gap-6"
        @submit="submitForm($event)"
    >
        @csrf

        <div class="grid gap-6">
            {{-- Name --}}
            <div class="grid gap-2">
                <label for="name" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-50">
                    Name
                </label>
                <input
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    autofocus
                    tabindex="1"
                    autocomplete="name"
                    placeholder="Full name"
                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                />
                @error('name')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div class="grid gap-2">
                <label for="email" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-50">
                    Email address
                </label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    tabindex="2"
                    autocomplete="email"
                    placeholder="email@example.com"
                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                />
                @error('email')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div class="grid gap-2" x-data="{ showPassword: false }">
                <label for="password" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-50">
                    Password
                </label>
                <div class="relative">
                    <input
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        name="password"
                        required
                        tabindex="3"
                        autocomplete="new-password"
                        placeholder="Password"
                        class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 pr-10 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                    />
                    <button
                        type="button"
                        tabindex="-1"
                        @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-muted-foreground hover:text-foreground transition-colors"
                    >
                        <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>
                        <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
                @error('password')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>

            {{-- Confirm Password --}}
            <div class="grid gap-2" x-data="{ showPassword: false }">
                <label for="password_confirmation" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-50">
                    Confirm password
                </label>
                <div class="relative">
                    <input
                        id="password_confirmation"
                        :type="showPassword ? 'text' : 'password'"
                        name="password_confirmation"
                        required
                        tabindex="4"
                        autocomplete="new-password"
                        placeholder="Confirm password"
                        class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 pr-10 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                    />
                    <button
                        type="button"
                        tabindex="-1"
                        @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-muted-foreground hover:text-foreground transition-colors"
                    >
                        <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>
                        <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
                @error('password_confirmation')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>

            {{-- Terms and Privacy Policy checkbox --}}
            <div class="flex flex-col gap-2">
                <div class="flex items-start gap-2">
                    <input
                        type="checkbox"
                        id="terms"
                        x-model="agreedToTerms"
                        class="mt-0.5 h-4 w-4 rounded border-input text-primary focus:ring-ring"
                    />
                    <label for="terms" class="text-sm font-normal leading-relaxed">
                        I have read and agree to the
                        <button
                            type="button"
                            class="underline underline-offset-2 font-medium text-foreground hover:text-foreground/80"
                            @click="activeTab = 'terms'; showTermsModal = true"
                        >
                            Terms and Conditions
                        </button>
                        and
                        <button
                            type="button"
                            class="underline underline-offset-2 font-medium text-foreground hover:text-foreground/80"
                            @click="activeTab = 'privacy'; showTermsModal = true"
                        >
                            Privacy Policy
                        </button>
                    </label>
                </div>
                <p x-show="termsError" x-text="termsError" class="text-sm text-destructive"></p>
            </div>

            {{-- Submit --}}
            <button
                type="submit"
                tabindex="5"
                class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2 mt-2 w-full"
            >
                Create account
            </button>
        </div>

        <div class="text-center text-sm text-muted-foreground">
            Already have an account?
            <a href="{{ route('login') }}" tabindex="6" class="hover:decoration-current! text-foreground underline underline-offset-4 decoration-neutral-300 transition-colors duration-300 ease-out dark:decoration-neutral-500">
                Log in
            </a>
        </div>
    </form>

    {{-- Terms and Privacy Policy Modal --}}
    <div
        x-show="showTermsModal"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center"
        x-transition.opacity
    >
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-black/50" @click="showTermsModal = false"></div>

        {{-- Dialog --}}
        <div class="relative z-50 max-h-[40vh] max-w-4xl w-full mx-4 bg-background rounded-lg border shadow-lg flex flex-col overflow-hidden">
            {{-- Header --}}
            <div class="p-6 pb-4">
                <h2 class="text-lg font-semibold leading-none tracking-tight" x-text="activeTab === 'terms' ? 'Terms and Conditions' : 'Privacy Policy'"></h2>
                <p class="text-sm text-muted-foreground mt-1.5">
                    Please review our policies carefully before creating your account.
                </p>
            </div>

            {{-- Tab switcher --}}
            <div class="flex border-b px-6">
                <button
                    @click="activeTab = 'terms'"
                    class="px-4 py-2.5 text-sm font-medium transition-colors"
                    :class="activeTab === 'terms'
                        ? 'border-b-2 border-primary text-foreground'
                        : 'text-muted-foreground hover:text-foreground'"
                >
                    Terms & Conditions
                </button>
                <button
                    @click="activeTab = 'privacy'"
                    class="px-4 py-2.5 text-sm font-medium transition-colors"
                    :class="activeTab === 'privacy'
                        ? 'border-b-2 border-primary text-foreground'
                        : 'text-muted-foreground hover:text-foreground'"
                >
                    Privacy Policy
                </button>
            </div>

            {{-- Scrollable content --}}
            <div class="overflow-y-auto flex-1 px-6 py-2 text-xs text-muted-foreground leading-normal">
                {{-- Terms and Conditions --}}
                <div x-show="activeTab === 'terms'" class="space-y-2">
                    <section>
                        <h3 class="text-sm font-semibold text-foreground mb-1">1. Acceptance of Terms</h3>
                        <p>
                            By accessing and using the BITSI Dispatch system (&ldquo;the Service&rdquo;), you agree to be bound by
                            these Terms and Conditions. If you do not agree to these terms, you may not access or use the Service.
                            These terms apply to all users, including drivers, dispatchers, administrators, and any other
                            authorized personnel.
                        </p>
                    </section>
                    <section>
                        <h3 class="text-sm font-semibold text-foreground mb-1">2. Account Registration</h3>
                        <p>
                            You must provide accurate, current, and complete information during the registration process.
                            You are responsible for maintaining the confidentiality of your account credentials and for all
                            activities that occur under your account. You agree to notify us immediately of any unauthorized
                            use of your account.
                        </p>
                    </section>
                    <section>
                        <h3 class="text-sm font-semibold text-foreground mb-1">3. Use of the Service</h3>
                        <p>
                            The BITSI Dispatch system is designed to facilitate bus dispatch operations, including trip
                            scheduling, vehicle assignment, driver management, and operational tracking. You agree to use
                            the Service only for its intended purpose and in compliance with all applicable laws and
                            regulations.
                        </p>
                        <p class="mt-1">You may not:</p>
                        <ul class="list-disc list-inside space-y-0.5 mt-0.5">
                            <li>Use the Service for any unlawful purpose or in violation of any organizational policies.</li>
                            <li>Attempt to gain unauthorized access to any part of the Service or its related systems.</li>
                            <li>Interfere with or disrupt the integrity or performance of the Service.</li>
                            <li>Transmit any viruses, malware, or other harmful code through the Service.</li>
                        </ul>
                    </section>
                    <section>
                        <h3 class="text-sm font-semibold text-foreground mb-1">4. Data Accuracy</h3>
                        <p>
                            Users are responsible for the accuracy of data entered into the system. BITSI Dispatch is not
                            liable for any operational issues, losses, or damages resulting from inaccurate or incomplete
                            data entry by users.
                        </p>
                    </section>
                    <section>
                        <h3 class="text-sm font-semibold text-foreground mb-1">5. Intellectual Property</h3>
                        <p>
                            The BITSI Dispatch system, including its design, source code, logos, and documentation, is the
                            intellectual property of Bicol Isarog Transport System, Inc. (BITSI). You may not reproduce,
                            distribute, modify, or create derivative works without express written permission.
                        </p>
                    </section>
                    <section>
                        <h3 class="text-sm font-semibold text-foreground mb-1">6. Limitation of Liability</h3>
                        <p>
                            BITSI shall not be liable for any indirect, incidental, special, consequential, or punitive
                            damages arising from your use of the Service. The Service is provided on an &ldquo;as is&rdquo;
                            and &ldquo;as available&rdquo; basis without warranties of any kind.
                        </p>
                    </section>
                    <section>
                        <h3 class="text-sm font-semibold text-foreground mb-1">7. Termination</h3>
                        <p>
                            We reserve the right to suspend or terminate your access to the Service at any time, with or
                            without cause, and with or without notice. Upon termination, your right to use the Service will
                            cease immediately.
                        </p>
                    </section>
                    <section>
                        <h3 class="text-sm font-semibold text-foreground mb-1">8. Modifications to Terms</h3>
                        <p>
                            We reserve the right to modify these Terms at any time. Continued use of the Service after
                            changes constitutes acceptance of the modified terms. Users will be notified of significant
                            changes through the system or via email.
                        </p>
                    </section>
                    <section>
                        <h3 class="text-sm font-semibold text-foreground mb-1">9. Governing Law</h3>
                        <p>
                            These Terms shall be governed by and construed in accordance with the laws of the Republic of
                            the Philippines. Any disputes arising from these terms shall be subject to the exclusive
                            jurisdiction of the courts of the Philippines.
                        </p>
                    </section>
                    <section>
                        <h3 class="text-sm font-semibold text-foreground mb-1">10. Contact</h3>
                        <p>
                            For questions regarding these Terms and Conditions, please contact the BITSI administration
                            through the official communication channels.
                        </p>
                    </section>
                </div>

                {{-- Privacy Policy --}}
                <div x-show="activeTab === 'privacy'" class="space-y-2">
                    <section>
                        <h3 class="text-sm font-semibold text-foreground mb-1">1. Information We Collect</h3>
                        <p>
                            In the course of operating the BITSI Dispatch system, we may collect the following types of
                            information:
                        </p>
                        <ul class="list-disc list-inside space-y-0.5 mt-0.5">
                            <li>
                                <strong>Personal Information:</strong> Name, email address, phone number, employee ID, and
                                other identifying details provided during account registration.
                            </li>
                            <li>
                                <strong>Operational Data:</strong> Trip schedules, vehicle assignments, driver logs, dispatch
                                records, timestamps, and other operational information entered into the system.
                            </li>
                            <li>
                                <strong>Usage Data:</strong> Login history, IP addresses, browser type, device information,
                                pages accessed, and actions performed within the system.
                            </li>
                        </ul>
                    </section>
                    <section>
                        <h3 class="text-sm font-semibold text-foreground mb-1">2. How We Use Your Information</h3>
                        <p>We use the collected information for the following purposes:</p>
                        <ul class="list-disc list-inside space-y-0.5 mt-0.5">
                            <li>To provide and maintain the BITSI Dispatch system and its features.</li>
                            <li>To manage user accounts and authenticate access to the system.</li>
                            <li>To facilitate dispatch operations, trip tracking, and reporting.</li>
                            <li>To communicate with users regarding system updates, operational notices, and account-related matters.</li>
                            <li>To monitor system usage for security, performance optimization, and audit purposes.</li>
                            <li>To comply with legal obligations and internal policies.</li>
                        </ul>
                    </section>
                    <section>
                        <h3 class="text-sm font-semibold text-foreground mb-1">3. Data Sharing and Disclosure</h3>
                        <p>
                            We do not sell, trade, or rent personal information to third parties. We may share information
                            only in the following circumstances:
                        </p>
                        <ul class="list-disc list-inside space-y-0.5 mt-0.5">
                            <li>With authorized BITSI personnel who require access to perform their duties.</li>
                            <li>With service providers who assist in operating the system, subject to confidentiality obligations.</li>
                            <li>When required by law, court order, or governmental regulation.</li>
                            <li>To protect the rights, property, or safety of BITSI, its employees, or the public.</li>
                        </ul>
                    </section>
                    <section>
                        <h3 class="text-sm font-semibold text-foreground mb-1">4. Data Security</h3>
                        <p>
                            We implement reasonable technical and organizational measures to protect your personal information
                            against unauthorized access, alteration, disclosure, or destruction. However, no method of
                            electronic storage or transmission is 100% secure, and we cannot guarantee absolute security.
                        </p>
                    </section>
                    <section>
                        <h3 class="text-sm font-semibold text-foreground mb-1">5. Data Retention</h3>
                        <p>
                            We retain personal information and operational data for as long as your account is active or as
                            needed to provide the Service. We may also retain certain information for legitimate business
                            purposes or as required by law, even after account termination.
                        </p>
                    </section>
                    <section>
                        <h3 class="text-sm font-semibold text-foreground mb-1">6. Your Rights</h3>
                        <p>
                            Under the Data Privacy Act of 2012 (Republic Act No. 10173) of the Philippines, you have the
                            following rights:
                        </p>
                        <ul class="list-disc list-inside space-y-0.5 mt-0.5">
                            <li>The right to be informed about the collection and processing of your personal data.</li>
                            <li>The right to access your personal data held by the organization.</li>
                            <li>The right to dispute inaccuracies and have your data corrected.</li>
                            <li>The right to request the deletion or blocking of your personal data under certain circumstances.</li>
                            <li>The right to file a complaint with the National Privacy Commission.</li>
                        </ul>
                    </section>
                    <section>
                        <h3 class="text-sm font-semibold text-foreground mb-1">7. Cookies and Tracking</h3>
                        <p>
                            The BITSI Dispatch system may use session cookies and similar technologies to maintain your
                            authenticated session and remember your preferences. These cookies are essential for the
                            operation of the system and do not track your activity outside of the Service.
                        </p>
                    </section>
                    <section>
                        <h3 class="text-sm font-semibold text-foreground mb-1">8. Third-Party Links</h3>
                        <p>
                            The Service may contain links to external sites that are not operated by us. We have no control
                            over the content and practices of these sites and are not responsible for their privacy policies.
                        </p>
                    </section>
                    <section>
                        <h3 class="text-sm font-semibold text-foreground mb-1">9. Changes to This Policy</h3>
                        <p>
                            We may update this Privacy Policy from time to time. Users will be notified of material changes
                            through the system or via email. Continued use of the Service after changes constitutes
                            acceptance of the updated policy.
                        </p>
                    </section>
                    <section>
                        <h3 class="text-sm font-semibold text-foreground mb-1">10. Contact Us</h3>
                        <p>
                            If you have questions or concerns about this Privacy Policy or how your data is handled, please
                            contact the BITSI Data Protection Officer through the official communication channels.
                        </p>
                    </section>
                </div>
            </div>

            {{-- Footer --}}
            <div class="p-4 pt-2 border-t flex justify-end">
                <button
                    type="button"
                    @click="showTermsModal = false"
                    class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2"
                >
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
@endsection