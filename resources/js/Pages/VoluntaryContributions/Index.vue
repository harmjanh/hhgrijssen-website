<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm } from '@inertiajs/vue3';

interface Props {
    registrationNumber?: string | null;
}

const props = defineProps<Props>();

const form = useForm({});

const requestRegistrationNumber = () => {
    if (confirm('Weet u zeker dat u een verzoek wilt sturen naar de penningmeester?')) {
        form.post(route('voluntary-contributions.request-registration-number'), {
            preserveScroll: true,
        });
    }
};
</script>

<template>

    <Head title="Vrijwillige Bijdragen" />

    <AuthenticatedLayout>
        <template #header>
            <PageHeader title="Vrijwillige Bijdragen" />
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <!-- Uitleg -->
                        <div class="mb-8">
                            <h2 class="text-2xl font-bold mb-4">Wat is een vrijwillige bijdrage?</h2>
                            <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                                Net zoals voorgaande jaren vraagt het college van kerkvoogden u om onze kerkelijke
                                gemeente in
                                2026 financieel te ondersteunen. De actie vrijwillige bijdrage 2026 loopt van januari
                                tot en met
                                december. In de brief die als bijlage aan deze mail is toegevoegd kunt u er meer over
                                lezen.
                            </p>
                            <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                                Een korte samenvatting: het college van kerkvoogden is voor de inkomsten vrijwel geheel
                                aangewezen op bijdragen en giften uit de gemeente. Een groot deel daarvan is afkomstig
                                uit de
                                jaarlijkse vrijwillige bijdragen.
                            </p>
                            <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                                Daar staat tegenover dat u als gemeente ons tot op dit moment nog altijd in staat hebt
                                gesteld
                                om aan alle financiële verplichtingen te kunnen voldoen. De kerkvoogdij is u hier erg
                                dankbaar
                                voor en we hopen dat u dit wilt blijven doen.
                            </p>
                        </div>

                        <!-- Instructies -->
                        <div class="mb-8">
                            <h2 class="text-2xl font-bold mb-4">Instructies</h2>
                            <ol class="list-decimal list-inside space-y-3 text-gray-700 dark:text-gray-300">
                                <li>Ga naar <a href="https://bijdragen.hhgrijssen.nl/" target="_blank"
                                        class="text-primary-600 hover:text-primary-800 underline">https://bijdragen.hhgrijssen.nl/</a>
                                </li>
                                <li>Vul uw postcode, huisnummer en geboortedatum in</li>
                                <li>Vul uw registratienummer in</li>
                                <li>Klik op 'Aanmelden'</li>
                                <li>Voer nu uw toezegging in</li>
                                <li>Klik op 'Bevestigen'</li>
                            </ol>
                        </div>

                        <!-- Registratienummer -->
                        <div class="mb-8 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <h3 class="text-lg font-semibold mb-2">Uw registratienummer</h3>
                            <div v-if="props.registrationNumber" class="mb-4">
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                    Uw registratienummer is:
                                </p>
                                <p class="text-2xl font-mono font-bold text-primary-600 dark:text-primary-400 mb-4">
                                    {{ props.registrationNumber }}
                                </p>
                            </div>
                            <div v-else class="mb-4">
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                    Uw registratienummer kon niet worden gevonden in onze database.
                                </p>
                            </div>
                            <PrimaryButton @click="requestRegistrationNumber" :disabled="form.processing"
                                class="w-full sm:w-auto">
                                Ik weet mijn registratienummer niet
                            </PrimaryButton>
                        </div>

                        <!-- Success message -->
                        <div v-if="$page.props.flash?.success"
                            class="mb-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                            <p class="text-green-800 dark:text-green-200">
                                {{ $page.props.flash.success }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
