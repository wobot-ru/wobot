<?php /* Smarty version 2.6.26, created on 2012-03-27 18:41:05
         compiled from _post.word-frequency.tpl */ ?>
<?php if ($this->_tpl_vars['post']->reply_count_cache > 19): ?>
<div id="word-frequency-div" class="section">
    <div>
        <h2>Most Popular Reply Keywords</h2>
        <div id="word-frequency-list" class="article">
            <div class="word-frequency-div" id="word-frequency-words">
            </div>
        <br style="clear : left;" />
        </div>
    </div>

    <div id="word-frequency-posts-div">
        <a href="#" onclick="return false;" id="word-frequency-close" class="linkbutton help">
        Close</a>
        <div id="word-frequency-posts" style="">
        </div>
    </div>
    <div style="clear: both;"></div>
</div>
<?php endif; ?>
