// build the helper div to display on mouse over
function BuildHelperDiv(a_DivID, a_IndicatorDivContent, a_Title, a_Text) {
    var l_Qutput = '';
    l_Qutput += '<span class="HelperDivIndicator" onMouseOver="ActivateHelperDiv($(this), \'' + a_Title + '\', \'' + escapeHtml(a_Text) + '\');" onMouseOut="$(\'#HelperDivContainer\').hide();" >' + a_IndicatorDivContent + '</span>';
    return l_Qutput;
}

// build the helper div to display on mouse over
function BuildHelperDivLink(a_DivID, a_IndicatorDivContent, a_Title, a_Text, a_SubTopic) {
    var l_Qutput = '';
    l_Qutput += '<a href="../common/help.php?subtopic=' + a_SubTopic + '" target="_blank" ><span class="HelperDivIndicator" onMouseOver="ActivateHelperDiv($(this), \'' + a_Title + '\', \'' + a_Text + '\', \'' + a_DivID + '\');" onMouseOut="$(\'#HelperDivContainer\').hide();" >' + a_IndicatorDivContent + '</span></a>';
    return l_Qutput;
}

// displays a helper div at the current mause position
function ActivateHelperDiv(a_Object, a_Title, a_Text, a_HelperDivPositionID) {
    // initialize variables
    var l_Left = 0;
    var l_Top = 0;
    var l_WindowHeight = $(window).height();
    var l_PageHeight = $(document).height();
    var l_ScrollTop = $(document).scrollTop();
    // set the new content of the tool tip
    $('#HelperDivHeadline').html(a_Title);
    $('#HelperDivText').html(a_Text);
    // check additional parameter and set the position
    if (a_HelperDivPositionID.length > 0) {
        l_Left = $('#' + a_HelperDivPositionID).offset().left;
        l_Top = $('#' + a_HelperDivPositionID).offset().top;
    } else {
        l_Left = (a_Object.offset().left + a_Object.parent().width());
        l_Top = a_Object.offset().top;
    }
    // get new tool tip height
    var l_ToolTipHeight = $('#HelperDivContainer').outerHeight(true);
    // check if the tool tip fits in the browser window
    if ((l_Top - l_ScrollTop + l_ToolTipHeight) > l_WindowHeight) {
        var l_TopBefore = l_Top;
        l_Top = (l_ScrollTop + l_WindowHeight - l_ToolTipHeight);
        if (l_Top < l_ScrollTop) {
            l_Top = l_ScrollTop;
        }
        $('.HelperDivArrow').css('top', (l_TopBefore - l_Top));
    } else {
        // console.log('# FIT#');
        $('.HelperDivArrow').css('top', -1);
    }
    // set position and display the tool tip
    $('#HelperDivContainer').css('top', l_Top);
    $('#HelperDivContainer').css('left', l_Left);
    $('#HelperDivContainer').show();
}

// toggle masked texts with readable texts
function ToggleMaskedText(a_TextFieldID) {
    m_DisplayedText = document.getElementById('Display' + a_TextFieldID).innerHTML;
    m_MaskedText = document.getElementById('Masked' + a_TextFieldID).innerHTML;
    m_ReadableText = document.getElementById('Readable' + a_TextFieldID).innerHTML;
    if (m_DisplayedText === m_MaskedText) {
        document.getElementById('Display' + a_TextFieldID).innerHTML = document.getElementById('Readable' + a_TextFieldID).innerHTML;
        document.getElementById('Button' + a_TextFieldID).src = JS_DIR_IMAGES + 'global/general/hide.gif';
    } else {
        document.getElementById('Display' + a_TextFieldID).innerHTML = document.getElementById('Masked' + a_TextFieldID).innerHTML;
        document.getElementById('Button' + a_TextFieldID).src = JS_DIR_IMAGES + 'global/general/show.gif';
    }
}

(function () {
    var storagePrefix = 'eclipseRightboxCollapsed:';

    function getBoxKey(box) {
        return box.getAttribute('data-eclipse-collapsible-box') || '';
    }

    function setBoxState(box, collapsed) {
        var button = box.querySelector('[data-eclipse-box-toggle]');

        box.classList.toggle('is-collapsed', collapsed);

        if (button) {
            button.textContent = collapsed ? '+' : '\u2212';
            button.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
            button.setAttribute('title', collapsed ? 'Maximizar' : 'Minimizar');
        }
    }

    function restoreBoxState(box) {
        var key = getBoxKey(box);

        if (!key) {
            return;
        }

        setBoxState(box, window.localStorage.getItem(storagePrefix + key) === '1');
    }

    function toggleBox(box) {
        var key = getBoxKey(box);
        var collapsed = !box.classList.contains('is-collapsed');

        setBoxState(box, collapsed);

        if (key) {
            window.localStorage.setItem(storagePrefix + key, collapsed ? '1' : '0');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        var boxes = document.querySelectorAll('[data-eclipse-collapsible-box]');

        boxes.forEach(function (box) {
            restoreBoxState(box);
        });
    });

    document.addEventListener('click', function (event) {
        var button = event.target && event.target.closest ? event.target.closest('[data-eclipse-box-toggle]') : null;

        if (!button) {
            return;
        }

        var box = button.closest('[data-eclipse-collapsible-box]');

        if (!box) {
            return;
        }

        event.preventDefault();
        event.stopPropagation();
        toggleBox(box);
    }, true);
})();

(function () {
    var loaderId = 'EclipsePageLoader';
    var activeClass = 'is-active';
    var showTimer = null;

    function getLoader() {
        return document.getElementById(loaderId);
    }

    function showLoader() {
        window.clearTimeout(showTimer);
        showTimer = window.setTimeout(function () {
            var loader = getLoader();
            if (!loader) {
                return;
            }

            loader.classList.add(activeClass);
            loader.setAttribute('aria-hidden', 'false');
        }, 80);
    }

    function hideLoader() {
        window.clearTimeout(showTimer);
        var loader = getLoader();
        if (!loader) {
            return;
        }

        loader.classList.remove(activeClass);
        loader.setAttribute('aria-hidden', 'true');
    }

    function cancelLoader() {
        hideLoader();
        if (typeof window.stop === 'function') {
            window.stop();
        }
    }

    function isModifiedClick(event) {
        return event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey;
    }

    function shouldHandleLink(link) {
        if (!link || link.hasAttribute('download')) {
            return false;
        }

        var target = (link.getAttribute('target') || '').toLowerCase();
        if (target && target !== '_self') {
            return false;
        }

        var href = link.getAttribute('href') || '';
        if (!href || href.charAt(0) === '#') {
            return false;
        }

        var normalized = href.trim().toLowerCase();
        return normalized.indexOf('javascript:') !== 0 &&
            normalized.indexOf('mailto:') !== 0 &&
            normalized.indexOf('tel:') !== 0;
    }

    document.addEventListener('click', function (event) {
        var closeButton = event.target && event.target.closest ? event.target.closest('[data-eclipse-loader-close]') : null;
        if (closeButton) {
            event.preventDefault();
            event.stopPropagation();
            cancelLoader();
            return;
        }

        if (isModifiedClick(event)) {
            return;
        }

        var target = event.target;
        var link = target && target.closest ? target.closest('a') : null;
        if (!link && target && target.parentElement && target.parentElement.closest) {
            link = target.parentElement.closest('a');
        }

        if (shouldHandleLink(link)) {
            showLoader();
        }
    }, true);

    document.addEventListener('submit', function (event) {
        if (event.defaultPrevented) {
            return;
        }

        var form = event.target;
        var target = form && (form.getAttribute('target') || '').toLowerCase();
        if (!target || target === '_self') {
            showLoader();
        }
    }, true);

    window.addEventListener('beforeunload', showLoader);
    window.addEventListener('pageshow', hideLoader);
    document.addEventListener('DOMContentLoaded', hideLoader);
})();
