<div>
    <div class="relative flex justify-center w-full">
        <button
            id="scrollLeftBtn"
            onclick="scrollKategori(-200)"
            class="absolute left-0 z-10 hidden -translate-y-1/2 shadow-md top-1/2 btn btn-circle bg-base-200">
            <svg class="w-6 h-6 text-gray-400 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 8 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 1 1.3 6.326a.91.91 0 0 0 0 1.348L7 13"></path>
            </svg>
        </button>

        <div
            id="scrollKategori"
            class="flex max-w-full gap-4 px-4 py-2 overflow-x-auto snap-x scroll-smooth no-scrollbar">
            @for ($i = 1; $i <= 2; $i++)
                <button class="snap-start rounded-2xl scroll-ml-6 btn btn-success whitespace-nowrap">Kategori 1</button>
                <button class="snap-start rounded-2xl scroll-ml-6 btn btn-success whitespace-nowrap">Kategori 2</button>
                <button class="snap-start rounded-2xl scroll-ml-6 btn btn-success whitespace-nowrap">Kategori 3</button>
                <button class="snap-start rounded-2xl scroll-ml-6 btn btn-success whitespace-nowrap">Kategori 4</button>
                <button class="snap-start rounded-2xl scroll-ml-6 btn btn-success whitespace-nowrap">Kategori 5</button>
                @endfor
        </div>

        <button
            id="scrollRightBtn"
            onclick="scrollKategori(200)"
            class="absolute right-0 z-10 hidden -translate-y-1/2 shadow-md top-1/2 btn btn-circle bg-base-200">
            <svg class="w-6 h-6 text-gray-400 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 8 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 13 5.7-5.326a.909.909 0 0 0 0-1.348L1 1"></path>
            </svg>
        </button>
    </div>

    <div class="flex flex-wrap justify-center gap-6 my-6">
        @for ($i = 1; $i <= 6; $i++)
            <div class="shadow-sm card bg-base-100 w-96">
            <figure class="px-10 pt-10">
                <img
                    src="https://img.daisyui.com/images/stock/photo-1606107557195-0e29a4b5b4aa.webp"
                    alt="Shoes"
                    class="rounded-xl" />
            </figure>
            <div class="items-center text-center card-body">
                <h2 class="card-title">Card Title</h2>
                <p>A card component has a figure, a body part, and inside body there are title and actions parts</p>
                <div class="card-actions">
                    <button class="btn btn-success">Buy Now</button>
                </div>
            </div>
    </div>
    @endfor

    @for ($i = 1; $i <= 6; $i++)
        <div class="shadow-sm card bg-base-100 w-96">
        <figure>
            <img
                src="https://img.daisyui.com/images/stock/photo-1606107557195-0e29a4b5b4aa.webp"
                alt="Shoes" />
        </figure>
        <div class="card-body">
            <h2 class="card-title">Card Title</h2>
            <p>A card component has a figure, a body part, and inside body there are title and actions parts</p>
            <div class="justify-end card-actions">
                <button class="btn btn-primary">Buy Now</button>
            </div>
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
