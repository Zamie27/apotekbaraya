<div>
    <div class="relative flex justify-center w-full mx-2 my-3">
        <div class="absolute left-0 z-10 -translate-y-1/2 rounded-full top-1/2 backdrop-blur-sm bg-base-200/60">
            <button
                id="scrollLeftBtn"
                onclick="scrollKategori(-200)"
                class="shadow-md btn btn-circle bg-base-200">
                <svg class="w-6 h-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 8 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 1 1.3 6.326a.91.91 0 0 0 0 1.348L7 13" />
                </svg>
            </button>
        </div>

        <div
            id="scrollKategori"
            class="flex max-w-full gap-4 px-4 py-2 overflow-x-auto snap-x scroll-smooth no-scrollbar">
            @for ($i = 1; $i <= 1; $i++)
                <button class="rounded-lg shadow-sm btn btn-success btn-md">Kategori 1</button>
                <button class="rounded-lg shadow-sm btn btn-success btn-md">Kategori 2</button>
                <button class="rounded-lg shadow-sm btn btn-success btn-md">Kategori 3</button>
                <button class="rounded-lg shadow-sm btn btn-success btn-md">Kategori 4</button>
                <button class="rounded-lg shadow-sm btn btn-success btn-md">Kategori 5</button>
                @endfor
        </div>

        <div class="absolute right-0 z-10 -translate-y-1/2 rounded-full top-1/2 backdrop-blur-sm bg-base-200/60">
            <button
                id="scrollRightBtn"
                onclick="scrollKategori(200)"
                class="shadow-md btn btn-circle bg-base-200">
                <svg class="w-6 h-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 8 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 13 5.7-5.326a.909.909 0 0 0 0-1.348L1 1" />
                </svg>
            </button>
        </div>
    </div>

    <div class="container flex flex-wrap justify-center gap-8 mx-auto my-5">

        @for ($i = 1; $i <= 20; $i++)

            <div class="card w-64 bg-base-100 shadow-xl group hover:shadow-2xl transition overflow-hidden">
            <figure class="relative">
                <a href="/pelanggan/produk">
                    <img src="https://img.daisyui.com/images/profile/demo/spiderperson@192.webp" alt="#" class="object-cover w-full h-64" />
                </a>

                <!-- Tombol -->
                <div class="absolute left-0 right-0 bottom-0 translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-in-out">
                    <button class="btn btn-success w-full font-bold rounded-none">
                        TAMBAH KE KERANJANG
                    </button>
                </div>
            </figure>
            <div class="card-body p-4">
                <a href="/pelanggan/produk" class="card-title text-base font-bold truncate hover:text-success">Nama Barang</a>
                <p class="text-base font-semibold text-gray-600">Rp 100.000</p>
            </div>
    </div>


    @endfor
</div>



<script>
    const scrollContainer = document.getElementById('scrollKategori');
    const scrollLeftBtn = document.getElementById('scrollLeftBtn');
    const scrollRightBtn = document.getElementById('scrollRightBtn');

    function updateButtonVisibility() {
        const scrollWidth = scrollContainer.scrollWidth;
        const clientWidth = scrollContainer.clientWidth;
        const scrollLeft = scrollContainer.scrollLeft;

        if (scrollWidth <= clientWidth) {
            scrollLeftBtn.classList.add('hidden');
            scrollRightBtn.classList.add('hidden');
        } else {
            scrollLeftBtn.classList.toggle('hidden', scrollLeft <= 0);
            scrollRightBtn.classList.toggle('hidden', (scrollLeft + clientWidth) >= scrollWidth - 1);
        }
    }

    function scrollKategori(amount) {
        scrollContainer.scrollBy({
            left: amount,
            behavior: 'smooth'
        });
    }

    window.addEventListener('load', updateButtonVisibility);
    window.addEventListener('resize', updateButtonVisibility);
    scrollContainer.addEventListener('scroll', updateButtonVisibility);
</script>


</div>