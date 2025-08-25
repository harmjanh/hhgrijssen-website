<template>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-b from-blue-50 to-gray-100 px-4">
        <div class="text-center">
            <h1 class="text-7xl md:text-9xl font-serif font-bold text-blue-200 drop-shadow mb-4">404</h1>
            <h2 class="text-2xl md:text-3xl font-semibold text-blue-900 mb-2">Pagina niet gevonden</h2>
            <p class="text-gray-600 mb-8 max-w-md mx-auto">De pagina die u zoekt bestaat niet of is verplaatst.
                Controleer het webadres of ga terug naar de startpagina.</p>
            <router-link to="/">
                <button
                    class="bg-primary hover:bg-primary/80 text-gray-900 font-semibold py-3 px-8 rounded shadow transition-colors duration-200 text-lg">
                    Terug naar home
                </button>
            </router-link>
        </div>
    </div>
</template>

<script setup lang="ts">
// No script logic needed for static 404 page
</script>

<style scoped>
.font-serif {
    font-family: 'Merriweather', 'Georgia', serif;
}
</style>
