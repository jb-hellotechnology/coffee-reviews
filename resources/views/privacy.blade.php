<x-app-layout>
    <x-slot name="title">Privacy Policy</x-slot>

    <x-slot name="header">
        <h1 class="font-display font-bold text-2xl text-gray-900">Privacy Policy</h1>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl border border-gray-200 p-8 prose prose-gray max-w-none">

                <p class="text-sm text-gray-400">Last updated: {{ date('d F Y') }}</p>

                <h2>Who we are</h2>
                <p>Coffee Shop Reviews is a project by Jack Barber (Hello Technology Ltd).</p>
                <p>Coffee Shop Reviews is a crowd-sourced review platform for coffee enthusiasts. Our website address is <a href="{{ config('app.url') }}">{{ config('app.url') }}</a>.</p>

                <h2>What data we collect</h2>
                <p>When you register on Coffee Shop Reviews we collect your name and email address. If you choose to complete your profile, we may also store a short biography, a website or social media link, and a profile photo you upload.</p>
                <p>When you submit a review we store the text of your review, the scores you give across our coffee dimensions, and the date and time of submission. Reviews are publicly visible and associated with your display name.</p>
                <p>We use cookies to keep you logged in between sessions. No third-party advertising cookies are used on this platform.</p>

                <h2>How we use your data</h2>
                <p>Your email address is used only to send transactional emails — account registration confirmation, review submission confirmation, and moderation notifications. We do not send marketing emails and we do not share your email address with third parties.</p>
                <p>Your name and any profile information you provide is displayed publicly on your profile page and alongside reviews you submit.</p>
                <p>Profile photos are stored on our server and are publicly accessible via your profile URL.</p>

                <h2>Third party services</h2>
                <p>We use the following third party services to operate the platform:</p>
                <ul>
                    <li><strong>Google Places API</strong> — used to look up venue details when a Google Maps URL is submitted. Google's privacy policy applies to this data.</li>
                    <li><strong>Anthropic Claude API</strong> — used to analyse the text of reviews and extract coffee-specific tags. Review text is sent to Anthropic's API for processing. Anthropic's privacy policy applies.</li>
                    <li><strong>Cloudflare Turnstile</strong> — used on the registration form to prevent spam. Cloudflare's privacy policy applies.</li>
                </ul>

                <h2>Data retention</h2>
                <p>Your account and associated data is retained for as long as your account remains active. If you delete your account, your personal data and reviews will be permanently removed from the platform.</p>

                <h2>Your rights</h2>
                <p>You have the right to access, correct or delete the personal data we hold about you. You can update your name, email, bio, website and profile photo at any time from your profile settings page. To request deletion of your account and all associated data, please contact us.</p>

                <h2>Cookies</h2>
                <p>We use a single session cookie to keep you logged in. This cookie is essential for the platform to function and does not track you across other websites. No advertising or analytics cookies are used.</p>

                <h2>Contact</h2>
                <p>If you have any questions about this privacy policy or the data we hold about you, please contact us at <a href="mailto:hello@coffeeshopreviews.co.uk">hello@coffeeshopreviews.co.uk</a>.</p>

            </div>
        </div>
    </div>
</x-app-layout>
