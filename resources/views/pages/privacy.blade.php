<x-layouts.app :title="'Privacy'">
<section class="max-w-4xl space-y-6">
    <div class="space-y-2">
        <h1 class="text-3xl font-bold text-slate-900">Privacy Notice</h1>
        <p class="text-sm text-slate-500">Last updated: 22 April 2026</p>
        <p class="text-slate-600">This Privacy Notice explains how CivicEase handles personal information within this university project prototype.</p>
    </div>

    <div class="card space-y-6 text-slate-600">
        <section class="space-y-2">
            <h2 class="text-xl font-semibold text-slate-900">1. Who We Are</h2>
            <p>CivicEase is an academic prototype created to demonstrate a community issue reporting platform. It is designed for project and demonstration purposes and is not presented as an official live council service.</p>
        </section>

        <section class="space-y-3">
            <h2 class="text-xl font-semibold text-slate-900">2. What Information We Collect</h2>
            <p>The platform currently collects information that users provide directly when creating an account or submitting a report.</p>
            <ul class="list-disc space-y-2 pl-6">
                <li><strong>Account information:</strong> name, email address, password hash, and optional address and postcode.</li>
                <li><strong>Report information:</strong> issue category, title, description, postcode, optional location details, latitude, longitude, optional notes, timestamps, and status.</li>
                <li><strong>Uploaded content:</strong> optional report images supplied by users.</li>
                <li><strong>Workflow records:</strong> status update history, including notes and the user account that made the update where applicable.</li>
            </ul>
        </section>

        <section class="space-y-3">
            <h2 class="text-xl font-semibold text-slate-900">3. How We Use Information</h2>
            <p>Information is used to operate the current prototype features and support the reporting workflow.</p>
            <ul class="list-disc space-y-2 pl-6">
                <li>Create and manage user accounts.</li>
                <li>Support login, email verification, and password reset.</li>
                <li>Allow users to submit and view their own reports.</li>
                <li>Display and validate location information, including postcode and map pin placement.</li>
                <li>Enable administrators to review reports and update statuses.</li>
            </ul>
        </section>

        <section class="space-y-2">
            <h2 class="text-xl font-semibold text-slate-900">4. Lawful Basis in Simple Terms</h2>
            <p>For this academic prototype, data is used only to run the features that users choose to use, such as account registration, report submission, and account recovery. In simple terms, information is processed because it is needed for the platform to provide those functions during project use and testing.</p>
        </section>

        <section class="space-y-2">
            <h2 class="text-xl font-semibold text-slate-900">5. Sharing of Information</h2>
            <p>CivicEase does not present itself as connected to any real council authority. Information entered into the prototype is reviewed within the system by authorised admin users only. The application also uses external services that support core features, such as postcode lookup and email delivery.</p>
        </section>

        <section class="space-y-2">
            <h2 class="text-xl font-semibold text-slate-900">6. Retention</h2>
            <p>Data is stored in the project database until it is updated or removed through the application workflow. Users can update profile information and may delete their account through the current system. No separate automated retention schedule is defined in the present implementation.</p>
        </section>

        <section class="space-y-2">
            <h2 class="text-xl font-semibold text-slate-900">7. Security</h2>
            <p>The platform stores passwords as hashes rather than plain text. Authentication, email verification, password reset, and server-side validation are also used as part of the current security approach. As this is an academic prototype, it should not be treated as a substitute for a full production security programme.</p>
        </section>

        <section class="space-y-3">
            <h2 class="text-xl font-semibold text-slate-900">8. User Rights</h2>
            <p>Within the current system, users can:</p>
            <ul class="list-disc space-y-2 pl-6">
                <li>view and update their own profile details;</li>
                <li>view their own submitted reports;</li>
                <li>delete their account through the account management workflow.</li>
            </ul>
        </section>

        <section class="space-y-2">
            <h2 class="text-xl font-semibold text-slate-900">9. Cookies and Session Data</h2>
            <p>CivicEase uses session data to maintain login state and support normal application use. This is part of the website’s core functionality rather than a separate marketing or analytics feature.</p>
        </section>

        <section class="space-y-3">
            <h2 class="text-xl font-semibold text-slate-900">10. External Services</h2>
            <p>The current prototype uses external services to support specific features.</p>
            <ul class="list-disc space-y-2 pl-6">
                <li><strong>Postcodes.io</strong> is used for postcode lookup and location assistance.</li>
                <li><strong>Leaflet and OpenStreetMap tiles</strong> are used for map display.</li>
                <li><strong>Brevo SMTP</strong> is used to send verification and password reset emails.</li>
                <li><strong>LM Studio</strong> may be used locally to support the website chatbot where enabled in the project environment.</li>
            </ul>
        </section>

        <section class="space-y-2">
            <h2 class="text-xl font-semibold text-slate-900">11. Changes to This Notice</h2>
            <p>This Privacy Notice may be updated if the project features or data handling approach change during development.</p>
        </section>

        <section class="space-y-2">
            <h2 class="text-xl font-semibold text-slate-900">12. Contact</h2>
            <p>If you need to raise a question about this project privacy notice, contact: <a href="mailto:civiceaseproject003@gmail.com" class="font-medium text-civic-700 hover:text-civic-900">civiceaseproject003@gmail.com</a></p>
        </section>
    </div>
</section>
</x-layouts.app>
