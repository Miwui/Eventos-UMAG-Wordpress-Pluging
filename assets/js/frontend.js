document.addEventListener('DOMContentLoaded', function () {
	const carousels = document.querySelectorAll('[data-umag-carousel="true"]');

	carousels.forEach(function (carousel) {
		const track = carousel.querySelector('.umag-events-carousel__track');
		const prev = carousel.querySelector('.umag-events-carousel__control--prev');
		const next = carousel.querySelector('.umag-events-carousel__control--next');

		if (!track || !prev || !next) {
			return;
		}

		const autoplay = carousel.dataset.umagCarouselAutoplay === 'true';
		const infinite = carousel.dataset.umagCarouselInfinite !== 'false';
		const pauseOnHover = carousel.dataset.umagCarouselPause === 'true';
		const allowDrag = carousel.dataset.umagCarouselDrag !== 'false';
		const delay = Math.max(parseInt(carousel.dataset.umagCarouselDelay || '4000', 10), 1000);
		const speed = Math.max(parseInt(carousel.dataset.umagCarouselSpeed || '900', 10), 150);
		const originals = Array.from(track.children);

		let autoplayTimer = null;
		let isAnimating = false;
		let isPointerDown = false;
		let startX = 0;
		let startScrollLeft = 0;
		let step = 320;
		let cloneCount = 1;

		if (originals.length <= 1) {
			prev.hidden = true;
			next.hidden = true;
			return;
		}

		const getGap = function () {
			const style = window.getComputedStyle(track);
			const columnGap = parseFloat(style.columnGap);
			const gap = parseFloat(style.gap);

			if (Number.isFinite(columnGap)) {
				return columnGap;
			}

			if (Number.isFinite(gap)) {
				return gap;
			}

			return 24;
		};

		const getStep = function () {
			const firstCard = track.querySelector('.umag-event-card');
			const width = firstCard ? firstCard.getBoundingClientRect().width : 0;
			step = width > 0 ? width + getGap() : 320;
			return step;
		};

		const getVisibleCount = function () {
			const width = getStep();
			const viewport = carousel.getBoundingClientRect().width;
			return Math.max(1, Math.ceil(viewport / Math.max(width, 1)));
		};

		const clearClones = function () {
			Array.from(track.querySelectorAll('[data-umag-clone="true"]')).forEach(function (clone) {
				clone.remove();
			});
		};

		const createClone = function (node) {
			const clone = node.cloneNode(true);
			clone.dataset.umagClone = 'true';
			return clone;
		};

		const buildTrack = function () {
			clearClones();
			cloneCount = infinite ? Math.min(originals.length, getVisibleCount() + 1) : 0;

			if (infinite) {
				for (let i = cloneCount; i > 0; i -= 1) {
					track.insertBefore(createClone(originals[originals.length - i]), track.firstChild);
				}

				for (let i = 0; i < cloneCount; i += 1) {
					track.appendChild(createClone(originals[i]));
				}
			}

			window.requestAnimationFrame(function () {
				const width = getStep();
				track.scrollLeft = infinite ? width * cloneCount : 0;
			});
		};

		const normalizeLoop = function () {
			if (!infinite) {
				return;
			}

			const width = getStep();
			const firstReal = width * cloneCount;
			const lastReal = width * (cloneCount + originals.length);

			if (track.scrollLeft < firstReal - width / 2) {
				track.scrollLeft += width * originals.length;
			} else if (track.scrollLeft >= lastReal - width / 2) {
				track.scrollLeft -= width * originals.length;
			}
		};

		const stopAutoplay = function () {
			if (autoplayTimer) {
				window.clearTimeout(autoplayTimer);
				autoplayTimer = null;
			}
		};

		const queueAutoplay = function () {
			stopAutoplay();

			if (!autoplay || isPointerDown || isAnimating) {
				return;
			}

			if ( ! infinite ) {
				const width = getStep();
				const lastSlideStart = Math.max( 0, width * Math.max( originals.length - getVisibleCount(), 0 ) );

				if ( track.scrollLeft >= lastSlideStart ) {
					return;
				}
			}

			autoplayTimer = window.setTimeout(function () {
				moveBy(1, true);
			}, delay);
		};

		const moveBy = function (direction, fromAutoplay) {
			if (isAnimating) {
				return;
			}

			isAnimating = true;
			stopAutoplay();

			const width = getStep();

			if (!infinite) {
				const maxScroll = Math.max(0, track.scrollWidth - track.clientWidth);
				const target = Math.max(0, Math.min(track.scrollLeft + width * direction, maxScroll));

				if (target === track.scrollLeft) {
					isAnimating = false;
					return;
				}

				track.scrollTo({
					left: target,
					behavior: 'smooth',
				});

				window.setTimeout(function () {
					isAnimating = false;

					if (fromAutoplay || autoplay) {
						queueAutoplay();
					}
				}, speed);

				return;
			}

			track.scrollTo({
				left: track.scrollLeft + width * direction,
				behavior: 'smooth',
			});

			window.setTimeout(function () {
				normalizeLoop();
				isAnimating = false;

				if (fromAutoplay || autoplay) {
					queueAutoplay();
				}
			}, speed);
		};

		prev.addEventListener('click', function () {
			moveBy(-1, false);
		});

		next.addEventListener('click', function () {
			moveBy(1, false);
		});

		if (pauseOnHover) {
			carousel.addEventListener('mouseenter', stopAutoplay);
			carousel.addEventListener('mouseleave', queueAutoplay);
		}

		if (allowDrag) {
			track.style.cursor = 'grab';

			track.addEventListener('pointerdown', function (event) {
				isPointerDown = true;
				startX = event.clientX;
				startScrollLeft = track.scrollLeft;
				track.style.cursor = 'grabbing';
				stopAutoplay();
			});

			track.addEventListener('pointermove', function (event) {
				if (!isPointerDown || isAnimating) {
					return;
				}

				const distance = event.clientX - startX;
				track.scrollLeft = startScrollLeft - distance;
			});

			const releasePointer = function () {
				if (!isPointerDown) {
					return;
				}

				isPointerDown = false;
				track.style.cursor = 'grab';
				normalizeLoop();
				queueAutoplay();
			};

			track.addEventListener('pointerup', releasePointer);
			track.addEventListener('pointercancel', releasePointer);
			track.addEventListener('pointerleave', releasePointer);
		}

		window.addEventListener('resize', function () {
			buildTrack();
			queueAutoplay();
		});

		buildTrack();
		queueAutoplay();
	});
});
