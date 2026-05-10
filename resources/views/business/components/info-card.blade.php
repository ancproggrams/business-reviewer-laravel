@php
    $websiteUrl = $business->website_url;
    if (! \Illuminate\Support\Str::startsWith($websiteUrl, ['http://', 'https://'])) {
        $websiteUrl = 'https://' . $websiteUrl;
    }
@endphp
<div class="card supplier-info-card">
    <ul>
        <li class="flex items-center pt-6 ">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                <title>link-72</title>
                <g fill="#333">
                    <path
                        d="M4.5,16c-1.2,0-2.3-0.5-3.2-1.3c-1.8-1.8-1.8-4.6,0-6.4L2,7.6L3.4,9L2.7,9.7 c-1,1-1,2.6,0,3.6c1,1,2.6,1,3.6,0l3-3c1-1,1-2.6,0-3.6L8.6,6L10,4.6l0.7,0.7c1.8,1.8,1.8,4.6,0,6.4l-3,3C6.9,15.5,5.7,16,4.5,16z">
                    </path>
                    <path fill="#333"
                        d="M6,11.4l-0.7-0.7c-1.8-1.8-1.8-4.6,0-6.4l3-3c0.9-0.9,2-1.3,3.2-1.3s2.3,0.5,3.2,1.3c1.8,1.8,1.8,4.6,0,6.4 L14,8.4L12.6,7l0.7-0.7c1-1,1-2.6,0-3.6c-1-1-2.6-1-3.6,0l-3,3c-1,1-1,2.6,0,3.6L7.4,10L6,11.4z">
                    </path>
                </g>
            </svg>
            <a class="ml-2" href="{{ $websiteUrl }}" target="_blank" rel="noopener">Website bekijken</a>
        </li>
        <hr class="my-5">
        <li class="flex items-center ">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                <title>phone-2</title>
                <g fill="#333">
                    <path fill="#333"
                        d="M15.285,12.305l-2.578-2.594c-0.39-0.393-1.025-0.393-1.416-0.002L9,12L4,7l2.294-2.294 c0.39-0.39,0.391-1.023,0.001-1.414l-2.58-2.584C3.324,0.317,2.691,0.317,2.3,0.708L0.004,3.003L0,3c0,7.18,5.82,13,13,13 l2.283-2.283C15.673,13.327,15.674,12.696,15.285,12.305z">
                    </path>
                </g>
            </svg>
            <span class="ml-2">{{ $business->phone_number }}</span>
        </li>
        <hr class="my-5">
        <li class="flex items-center pb-6">
            <svg xmlns=" http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                <title>chat-46</title>
                <g fill="#333">
                    <path
                        d="M15,4h-1v6c0,0.552-0.448,1-1,1H6.828L5,13h5l3,3v-3h2c0.552,0,1-0.448,1-1V5 C16,4.448,15.552,4,15,4z">
                    </path>
                    <path fill="#333"
                        d="M1,0h10c0.552,0,1,0.448,1,1v7c0,0.552-0.448,1-1,1H6l-3,3V9H1C0.448,9,0,8.552,0,8V1C0,0.448,0.448,0,1,0z">
                    </path>
                </g>
            </svg>
            <a class="ml-2" href="mailto:{{ $business->email }}">Mail leverancier</a>
        </li>
    </ul>
</div>
