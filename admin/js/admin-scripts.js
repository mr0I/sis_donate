jQuery(document).ready(function ($) {

    // search author
    const searchAuthorForm = document.getElementById('search_author_frm');
    const searchAuthorInput = document.getElementById('search_author_input');
    const formAction = $(searchAuthorForm).attr('action');
    let callbackUrl = formAction.split('&')[0];

    const url = new URL(window.location.href);
    const sortBy = url.searchParams.get('sort_by') || '';

    $(searchAuthorForm).on('submit', function () {
        let input = $(searchAuthorInput).val();

        if (input !== '') callbackUrl += '&s=' +  input;
        if (sortBy !== '') callbackUrl +=  '&sort_by=' +  sortBy;
        callbackUrl += '&page_num=1';

        $(searchAuthorForm).attr('action',callbackUrl);
    });

});