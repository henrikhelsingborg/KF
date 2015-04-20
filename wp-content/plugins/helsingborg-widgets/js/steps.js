jQuery(document).ready(function($) {

	if ($('.guide-list li.current:first-child').length) {
		$('.prev-step').hide();
	}

	$('.next-step').on('click', function(e) {
		e.preventDefault();

		var currentElem = $('.guide-list li.current');

		currentElem.next().addClass("current");
		currentElem.removeClass("current");
		removeNext();
		removePrev();
		setPager();
	});

	$('.prev-step').on('click', function(e) {
		e.preventDefault();

		var currentElem = $('.guide-list li.current');

		currentElem.prev().addClass("current");
		currentElem.removeClass("current");
		removePrev();
		removeNext();
		setPager();
	});

	$('.pagination li').on('click', function(e) {
		if (this.innerText.length <= 3 && this.innerText.length > 0) {
			e.preventDefault();

			var currentElem = $('.guide-list li.current');
			var newElem = $('.guide-list > li').not('.guide-list li ul li').eq(
				parseInt(this.innerText) - 1);

			currentElem.removeClass("current");
			newElem.addClass("current");
			removePrev();
			removeNext();
			setPager();
		}
	});

	function setPager() {
		var newIndex = $('.guide-list > li').not('.guide-list li ul li').index($(
			'.guide-list li.current'));

		$('.pagination li.current-pager').removeClass('current-pager');
		$('.pagination li').eq(newIndex + 1).addClass('current-pager');
	}

	function removeNext() {
		if ($('.guide-list li.current:last-child').length) {
			$('.next-step').hide();
		} else {
			$('.next-step').show();
		}

	}

	function removePrev() {
		if ($('.guide-list li.current:first-child').length) {
			$('.prev-step').hide();
		} else {
			$('.prev-step').show();
		}
	}

});
