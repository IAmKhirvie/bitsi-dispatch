<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import TermsModal from '@/components/TermsModal.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { ref } from 'vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const agreedToTerms = ref(false);
const showTermsModal = ref(false);
const termsError = ref('');

const submit = () => {
    if (!agreedToTerms.value) {
        termsError.value = 'You must agree to the Terms and Conditions and Privacy Policy.';
        return;
    }
    termsError.value = '';
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <AuthBase title="Create an account" description="Enter your details below to create your account">
        <Head title="Register" />

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="name">Name</Label>
                    <Input id="name" type="text" required autofocus tabindex="1" autocomplete="name" v-model="form.name" placeholder="Full name" />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">Email address</Label>
                    <Input id="email" type="email" required tabindex="2" autocomplete="email" v-model="form.email" placeholder="email@example.com" />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Password</Label>
                    <Input
                        id="password"
                        type="password"
                        required
                        tabindex="3"
                        autocomplete="new-password"
                        v-model="form.password"
                        placeholder="Password"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">Confirm password</Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        required
                        tabindex="4"
                        autocomplete="new-password"
                        v-model="form.password_confirmation"
                        placeholder="Confirm password"
                    />
                    <InputError :message="form.errors.password_confirmation" />
                </div>

                <!-- Terms and Privacy Policy checkbox -->
                <div class="flex flex-col gap-2">
                    <div class="flex items-start gap-2">
                        <Checkbox
                            id="terms"
                            v-model:checked="agreedToTerms"
                            class="mt-0.5"
                        />
                        <Label for="terms" class="text-sm font-normal leading-relaxed">
                            I have read and agree to the
                            <button
                                type="button"
                                class="underline underline-offset-2 hover:text-foreground font-medium text-foreground"
                                @click="showTermsModal = true"
                            >
                                Terms and Conditions
                            </button>
                            and
                            <button
                                type="button"
                                class="underline underline-offset-2 hover:text-foreground font-medium text-foreground"
                                @click="showTermsModal = true"
                            >
                                Privacy Policy
                            </button>
                        </Label>
                    </div>
                    <p v-if="termsError" class="text-sm text-destructive">{{ termsError }}</p>
                </div>

                <Button type="submit" class="mt-2 w-full" tabindex="5" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Create account
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                Already have an account?
                <TextLink :href="route('login')" class="underline underline-offset-4" tabindex="6">Log in</TextLink>
            </div>
        </form>

        <!-- Terms and Privacy Policy Modal -->
        <TermsModal v-model:open="showTermsModal" />
    </AuthBase>
</template>