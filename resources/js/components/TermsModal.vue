<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { ref } from 'vue';

defineProps<{
    open: boolean;
}>();

const emit = defineEmits<{
    'update:open': [value: boolean];
}>();

const activeTab = ref<'terms' | 'privacy'>('terms');

function close() {
    emit('update:open', false);
}
</script>

<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent class="max-h-[80vh] max-w-2xl overflow-hidden flex flex-col">
            <DialogHeader>
                <DialogTitle>{{ activeTab === 'terms' ? 'Terms and Conditions' : 'Privacy Policy' }}</DialogTitle>
                <DialogDescription>
                    Please review our policies carefully before creating your account.
                </DialogDescription>
            </DialogHeader>

            <!-- Tab switcher -->
            <div class="flex border-b">
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

            <!-- Scrollable content area -->
            <div class="overflow-y-auto flex-1 pr-2 text-sm text-muted-foreground leading-relaxed">
                <!-- Terms and Conditions -->
                <div v-if="activeTab === 'terms'" class="space-y-4">
                    <section>
                        <h3 class="text-base font-semibold text-foreground mb-2">1. Acceptance of Terms</h3>
                        <p>
                            By accessing and using the BITSI Dispatch system (&ldquo;the Service&rdquo;), you agree to be bound by
                            these Terms and Conditions. If you do not agree to these terms, you may not access or use the Service.
                            These terms apply to all users, including drivers, dispatchers, administrators, and any other
                            authorized personnel.
                        </p>
                    </section>

                    <section>
                        <h3 class="text-base font-semibold text-foreground mb-2">2. Account Registration</h3>
                        <p>
                            You must provide accurate, current, and complete information during the registration process.
                            You are responsible for maintaining the confidentiality of your account credentials and for all
                            activities that occur under your account. You agree to notify us immediately of any unauthorized
                            use of your account.
                        </p>
                    </section>

                    <section>
                        <h3 class="text-base font-semibold text-foreground mb-2">3. Use of the Service</h3>
                        <p>
                            The BITSI Dispatch system is designed to facilitate bus dispatch operations, including trip
                            scheduling, vehicle assignment, driver management, and operational tracking. You agree to use
                            the Service only for its intended purpose and in compliance with all applicable laws and
                            regulations.
                        </p>
                        <p class="mt-2">
                            You may not:
                        </p>
                        <ul class="list-disc list-inside space-y-1 mt-1">
                            <li>Use the Service for any unlawful purpose or in violation of any organizational policies.</li>
                            <li>Attempt to gain unauthorized access to any part of the Service or its related systems.</li>
                            <li>Interfere with or disrupt the integrity or performance of the Service.</li>
                            <li>Transmit any viruses, malware, or other harmful code through the Service.</li>
                        </ul>
                    </section>

                    <section>
                        <h3 class="text-base font-semibold text-foreground mb-2">4. Data Accuracy</h3>
                        <p>
                            Users are responsible for the accuracy of data entered into the system. BITSI Dispatch is not
                            liable for any operational issues, losses, or damages resulting from inaccurate or incomplete
                            data entry by users.
                        </p>
                    </section>

                    <section>
                        <h3 class="text-base font-semibold text-foreground mb-2">5. Intellectual Property</h3>
                        <p>
                            The BITSI Dispatch system, including its design, source code, logos, and documentation, is the
                            intellectual property of Bicol Isarog Transport System, Inc. (BITSI). You may not reproduce,
                            distribute, modify, or create derivative works without express written permission.
                        </p>
                    </section>

                    <section>
                        <h3 class="text-base font-semibold text-foreground mb-2">6. Limitation of Liability</h3>
                        <p>
                            BITSI shall not be liable for any indirect, incidental, special, consequential, or punitive
                            damages arising from your use of the Service. The Service is provided on an &ldquo;as is&rdquo;
                            and &ldquo;as available&rdquo; basis without warranties of any kind.
                        </p>
                    </section>

                    <section>
                        <h3 class="text-base font-semibold text-foreground mb-2">7. Termination</h3>
                        <p>
                            We reserve the right to suspend or terminate your access to the Service at any time, with or
                            without cause, and with or without notice. Upon termination, your right to use the Service will
                            cease immediately.
                        </p>
                    </section>

                    <section>
                        <h3 class="text-base font-semibold text-foreground mb-2">8. Modifications to Terms</h3>
                        <p>
                            We reserve the right to modify these Terms at any time. Continued use of the Service after
                            changes constitutes acceptance of the modified terms. Users will be notified of significant
                            changes through the system or via email.
                        </p>
                    </section>

                    <section>
                        <h3 class="text-base font-semibold text-foreground mb-2">9. Governing Law</h3>
                        <p>
                            These Terms shall be governed by and construed in accordance with the laws of the Republic of
                            the Philippines. Any disputes arising from these terms shall be subject to the exclusive
                            jurisdiction of the courts of the Philippines.
                        </p>
                    </section>

                    <section>
                        <h3 class="text-base font-semibold text-foreground mb-2">10. Contact</h3>
                        <p>
                            For questions regarding these Terms and Conditions, please contact the BITSI administration
                            through the official communication channels.
                        </p>
                    </section>
                </div>

                <!-- Privacy Policy -->
                <div v-if="activeTab === 'privacy'" class="space-y-4">
                    <section>
                        <h3 class="text-base font-semibold text-foreground mb-2">1. Information We Collect</h3>
                        <p>
                            In the course of operating the BITSI Dispatch system, we may collect the following types of
                            information:
                        </p>
                        <ul class="list-disc list-inside space-y-1 mt-1">
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
                        <h3 class="text-base font-semibold text-foreground mb-2">2. How We Use Your Information</h3>
                        <p>We use the collected information for the following purposes:</p>
                        <ul class="list-disc list-inside space-y-1 mt-1">
                            <li>To provide and maintain the BITSI Dispatch system and its features.</li>
                            <li>To manage user accounts and authenticate access to the system.</li>
                            <li>To facilitate dispatch operations, trip tracking, and reporting.</li>
                            <li>To communicate with users regarding system updates, operational notices, and account-related matters.</li>
                            <li>To monitor system usage for security, performance optimization, and audit purposes.</li>
                            <li>To comply with legal obligations and internal policies.</li>
                        </ul>
                    </section>

                    <section>
                        <h3 class="text-base font-semibold text-foreground mb-2">3. Data Sharing and Disclosure</h3>
                        <p>
                            We do not sell, trade, or rent personal information to third parties. We may share information
                            only in the following circumstances:
                        </p>
                        <ul class="list-disc list-inside space-y-1 mt-1">
                            <li>With authorized BITSI personnel who require access to perform their duties.</li>
                            <li>With service providers who assist in operating the system, subject to confidentiality obligations.</li>
                            <li>When required by law, court order, or governmental regulation.</li>
                            <li>To protect the rights, property, or safety of BITSI, its employees, or the public.</li>
                        </ul>
                    </section>

                    <section>
                        <h3 class="text-base font-semibold text-foreground mb-2">4. Data Security</h3>
                        <p>
                            We implement reasonable technical and organizational measures to protect your personal information
                            against unauthorized access, alteration, disclosure, or destruction. However, no method of
                            electronic storage or transmission is 100% secure, and we cannot guarantee absolute security.
                        </p>
                    </section>

                    <section>
                        <h3 class="text-base font-semibold text-foreground mb-2">5. Data Retention</h3>
                        <p>
                            We retain personal information and operational data for as long as your account is active or as
                            needed to provide the Service. We may also retain certain information for legitimate business
                            purposes or as required by law, even after account termination.
                        </p>
                    </section>

                    <section>
                        <h3 class="text-base font-semibold text-foreground mb-2">6. Your Rights</h3>
                        <p>Under the Data Privacy Act of 2012 (Republic Act No. 10173) of the Philippines, you have the
                            following rights:
                        </p>
                        <ul class="list-disc list-inside space-y-1 mt-1">
                            <li>The right to be informed about the collection and processing of your personal data.</li>
                            <li>The right to access your personal data held by the organization.</li>
                            <li>The right to dispute inaccuracies and have your data corrected.</li>
                            <li>The right to request the deletion or blocking of your personal data under certain circumstances.</li>
                            <li>The right to file a complaint with the National Privacy Commission.</li>
                        </ul>
                    </section>

                    <section>
                        <h3 class="text-base font-semibold text-foreground mb-2">7. Cookies and Tracking</h3>
                        <p>
                            The BITSI Dispatch system may use session cookies and similar technologies to maintain your
                            authenticated session and remember your preferences. These cookies are essential for the
                            operation of the system and do not track your activity outside of the Service.
                        </p>
                    </section>

                    <section>
                        <h3 class="text-base font-semibold text-foreground mb-2">8. Third-Party Links</h3>
                        <p>
                            The Service may contain links to external sites that are not operated by us. We have no control
                            over the content and practices of these sites and are not responsible for their privacy policies.
                        </p>
                    </section>

                    <section>
                        <h3 class="text-base font-semibold text-foreground mb-2">9. Changes to This Policy</h3>
                        <p>
                            We may update this Privacy Policy from time to time. Users will be notified of material changes
                            through the system or via email. Continued use of the Service after changes constitutes
                            acceptance of the updated policy.
                        </p>
                    </section>

                    <section>
                        <h3 class="text-base font-semibold text-foreground mb-2">10. Contact Us</h3>
                        <p>
                            If you have questions or concerns about this Privacy Policy or how your data is handled, please
                            contact the BITSI Data Protection Officer through the official communication channels.
                        </p>
                    </section>
                </div>
            </div>

            <DialogFooter class="pt-2">
                <Button @click="close">Close</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>