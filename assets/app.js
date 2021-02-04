/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';

// start the Stimulus application
import './bootstrap';

import 'bootstrap';
import $ from 'jquery';
import InfiniteScroll from 'infinite-scroll';

if (document.querySelector('.infinite-scroll-next-page') !== null) {
    const infiniteScroll = new InfiniteScroll('#main-restaurants-list', {
        path: '.infinite-scroll-next-page',
        append: '.card-restaurant',
        hideNav: '.pagination',
        status: '.scroller-status',
        history: false
    });
}