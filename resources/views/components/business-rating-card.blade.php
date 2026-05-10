 <div class="card mt-4 supplier-rating-card">
     <div class="supplier-rating-card__header">
         <span>Reviewverdeling</span>
         <strong>{{ number_format($business->average_review, 1, ',', '.') }}</strong>
     </div>
     <ul class="review-ratings">
         @foreach ([5, 4, 3, 2, 1] as $rating)
         <li>
             <span class="review-ratings__label">{{ $rating }}</span>
             <div class="review-ratings__track">
                 <span style="width: {{ $ratingsArray[$rating][1] ?: 0 }}%"></span>
             </div>
             <span class="review-ratings__count">{{ $ratingsArray[$rating][0] }}</span>
         </li>
         @endforeach
     </ul>
 </div>
