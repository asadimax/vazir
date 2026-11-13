/**
 * رفتار سربرگ: منوی کشویی موبایل، جست‌وجوی سریع، پنهان‌شدن سربرگ هنگام اسکرول رو به پایین،
 * زیرمنوی لمسی، حالت روشن/تاریک و دکمهٔ بازگشت به بالا.
 */
( function () {
	'use strict';

	var root = document.documentElement;
	var header = document.getElementById( 'header' );
	var nav = document.getElementById( 'primary-nav' );
	var menuBtn = document.querySelector( '.menu-btn' );
	var closeBtn = document.querySelector( '.nav-close' );
	var overlay = document.querySelector( '.nav-overlay' );
	var searchBtn = document.querySelector( '.search-btn' );
	var searchBox = document.getElementById( 'mobile-search' );
	var mq = window.matchMedia ? window.matchMedia( '(max-width: 720px)' ) : { matches: false };
	var menuWasOpen = false;

	function showHeader() {
		if ( header ) {
			header.classList.remove( 'is-hidden' );
		}
	}

	/* ---------- منوی کشویی ---------- */
	function setMenu( open ) {
		if ( ! nav ) {
			return;
		}
		nav.classList.toggle( 'is-open', open );
		if ( overlay ) {
			overlay.classList.toggle( 'is-open', open );
		}
		root.classList.toggle( 'nav-open', open );
		if ( menuBtn ) {
			menuBtn.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			menuBtn.setAttribute( 'aria-label', open ? 'بستن منو' : 'باز کردن منو' );
		}
		if ( open ) {
			showHeader();
			if ( closeBtn ) {
				closeBtn.focus();
			}
		} else if ( menuWasOpen && menuBtn ) {
			menuBtn.focus();
		}
		menuWasOpen = open;
	}

	function setSearch( open ) {
		if ( ! searchBox || ! searchBtn ) {
			return;
		}
		searchBox.hidden = ! open;
		searchBtn.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		if ( open ) {
			showHeader();
			var field = searchBox.querySelector( 'input[type="search"]' );
			if ( field ) {
				field.focus();
			}
		}
	}

	if ( menuBtn ) {
		menuBtn.addEventListener( 'click', function () {
			setMenu( ! nav.classList.contains( 'is-open' ) );
		} );
	}
	if ( closeBtn ) {
		closeBtn.addEventListener( 'click', function () {
			setMenu( false );
		} );
	}
	if ( overlay ) {
		overlay.addEventListener( 'click', function () {
			setMenu( false );
		} );
	}
	if ( searchBtn ) {
		searchBtn.addEventListener( 'click', function () {
			setSearch( searchBox.hidden );
		} );
	}
	// دسکتاپ: کلیک روی پس‌زمینهٔ تیره یا دکمهٔ ✕ پنجرهٔ جست‌وجو را می‌بندد.
	if ( searchBox ) {
		searchBox.addEventListener( 'click', function ( e ) {
			if ( e.target === searchBox || e.target.closest( '.search-close' ) ) {
				setSearch( false );
				if ( searchBtn ) {
					searchBtn.focus();
				}
			}
		} );
	}

	document.addEventListener( 'keydown', function ( e ) {
		if ( 'Escape' === e.key ) {
			if ( nav && nav.classList.contains( 'is-open' ) ) {
				setMenu( false );
			} else if ( searchBox && ! searchBox.hidden ) {
				setSearch( false );
				if ( searchBtn ) {
					searchBtn.focus();
				}
			}
			return;
		}

		// نگه‌داشتن فوکوس صفحه‌کلید داخل منوی بازشده.
		if ( 'Tab' === e.key && nav && nav.classList.contains( 'is-open' ) ) {
			var items = Array.prototype.filter.call(
				nav.querySelectorAll( 'a[href], button' ),
				function ( el ) {
					return null !== el.offsetParent;
				}
			);
			if ( ! items.length ) {
				return;
			}
			var first = items[0];
			var last = items[ items.length - 1 ];
			if ( e.shiftKey && document.activeElement === first ) {
				e.preventDefault();
				last.focus();
			} else if ( ! e.shiftKey && document.activeElement === last ) {
				e.preventDefault();
				first.focus();
			}
		}
	} );

	// با رفتن به عرض دسکتاپ، همه‌چیز به حالت اول برگردد.
	var onBreakpoint = function () {
		if ( ! mq.matches ) {
			setMenu( false );
			menuWasOpen = false;
			if ( searchBox ) {
				searchBox.hidden = true;
			}
			if ( searchBtn ) {
				searchBtn.setAttribute( 'aria-expanded', 'false' );
			}
			showHeader();
		}
	};
	if ( mq.addEventListener ) {
		mq.addEventListener( 'change', onBreakpoint );
	} else if ( mq.addListener ) {
		mq.addListener( onBreakpoint );
	}

	/* ---------- زیرمنو (آکاردئون در موبایل، دکمه برای صفحه‌کلید) ---------- */
	if ( nav ) {
		var parents = nav.querySelectorAll( '.menu-item-has-children, .page_item_has_children' );
		Array.prototype.forEach.call( parents, function ( li ) {
			var link = li.querySelector( ':scope > a' );
			if ( ! link ) {
				return;
			}
			var btn = document.createElement( 'button' );
			btn.type = 'button';
			btn.className = 'submenu-toggle';
			btn.setAttribute( 'aria-expanded', 'false' );
			btn.setAttribute( 'aria-label', 'باز کردن زیرمنو' );
			btn.addEventListener( 'click', function () {
				var open = li.classList.toggle( 'open' );
				btn.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			} );
			link.parentNode.insertBefore( btn, link.nextSibling );
		} );
	}

	/* ---------- سربرگ چسبان: پنهان با اسکرول رو به پایین، نمایان با اسکرول رو به بالا ---------- */
	if ( header ) {
		var lastY = window.pageYOffset;
		var ticking = false;

		var update = function () {
			ticking = false;
			if ( ! mq.matches ) {
				return;
			}
			var y = window.pageYOffset;
			var busy = ( nav && nav.classList.contains( 'is-open' ) ) || ( searchBox && ! searchBox.hidden );
			if ( busy || y < 120 || y < lastY - 6 ) {
				showHeader();
			} else if ( y > lastY + 8 ) {
				header.classList.add( 'is-hidden' );
			}
			lastY = y;
		};

		window.addEventListener(
			'scroll',
			function () {
				if ( ! ticking ) {
					ticking = true;
					window.requestAnimationFrame( update );
				}
			},
			{ passive: true }
		);

		// با فوکوس صفحه‌کلید روی عناصر سربرگ، سربرگ نمایان بماند.
		header.addEventListener( 'focusin', showHeader );
	}

	/* ---------- حالت روشن/تاریک ---------- */
	var themeBtn = document.querySelector( '.theme-toggle' );
	if ( themeBtn ) {
		var sync = function () {
			var dark = 'dark' === root.getAttribute( 'data-theme' );
			themeBtn.setAttribute( 'aria-pressed', dark ? 'true' : 'false' );
			themeBtn.setAttribute( 'aria-label', dark ? 'تغییر به حالت روشن' : 'تغییر به حالت تاریک' );
		};
		sync();
		themeBtn.addEventListener( 'click', function () {
			var next = 'dark' === root.getAttribute( 'data-theme' ) ? 'light' : 'dark';
			root.setAttribute( 'data-theme', next );
			try {
				localStorage.setItem( 'mfa-theme', next );
			} catch ( e ) {}
			sync();
		} );
	}

	/* ---------- بازگشت به بالا ---------- */
	var top = document.querySelector( '.go-top' );
	if ( top ) {
		var onScroll = function () {
			top.classList.toggle( 'visible', window.pageYOffset > 400 );
		};
		window.addEventListener( 'scroll', onScroll, { passive: true } );
		onScroll();
	}
}() );
