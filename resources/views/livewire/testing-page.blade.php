<section class="w-full">
    <!-- resources/views/components/modal.blade.php -->
    <div class="relative">
        <!-- Tombol untuk membuka modal -->
        <button id="openModal" class="bg-blue-500 text-white px-4 py-2 rounded">
            Buka Modal
        </button>

        <!-- Modal dengan position absolute -->
        <div id="modal"
            class="hidden absolute top-10 left-0 bg-white p-6 rounded shadow-lg border border-gray-200 w-64">
            <h3 class="text-lg font-semibold mb-2">Informasi</h3>
            <p class="mb-4">Ini adalah contoh modal dengan position absolute.</p>
            <button id="closeModal" class="bg-red-500 text-white px-3 py-1 rounded text-sm">
                Tutup
            </button>
        </div>
    </div>
 d
    <script>
        const openModal = document.getElementById('openModal');
        const closeModal = document.getElementById('closeModal');
        const modal = document.getElementById('modal');

        openModal.addEventListener('click', () => {
            modal.classList.remove('hidden');
        });

        closeModal.addEventListener('click', () => {
            modal.classList.add('hidden');
        });
    </script>
</section>
