<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
    "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <title><?php echo $this->benchmark_title; ?> - Benchmark</title>

        <script type="text/javascript">
            function toggleCode(doc, id) {
                var code_elem = doc.getElementById('code-' + id);
                
                if ( code_elem.style.display != 'block' ) {
                    code_elem.style.display = 'block';
                } else {
                    code_elem.style.display = 'none';
                }
            }
        </script>
        <style type="text/css">
            /* http://meyerweb.com/eric/tools/css/reset/ */
            /* v1.0 | 20080212 */

            html, body, div, span, applet, object, iframe,
            h1, h2, h3, h4, h5, h6, p, blockquote, pre,
            a, abbr, acronym, address, big, cite, code,
            del, dfn, em, font, img, ins, kbd, q, s, samp,
            small, strike, strong, sub, sup, tt, var,
            b, u, i, center,
            dl, dt, dd, ol, ul, li,
            fieldset, form, label, legend,
            table, caption, tbody, tfoot, thead, tr, th, td {
                    margin: 0;
                    padding: 0;
                    border: 0;
                    outline: 0;
                    font-size: 100%;
                    vertical-align: baseline;
                    background: transparent;
            }
            body {
                    line-height: 1;
            }
            ol, ul {
                    list-style: none;
            }
            blockquote, q {
                    quotes: none;
            }
            blockquote:before, blockquote:after,
            q:before, q:after {
                    content: '';
                    content: none;
            }

            /* remember to define focus styles! */
            :focus {
                    outline: 0;
            }

            /* remember to highlight inserts somehow! */
            ins {
                    text-decoration: none;
            }
            del {
                    text-decoration: line-through;
            }

            /* tables still need 'cellspacing="0"' in the markup */
            table {
                    border-collapse: collapse;
                    border-spacing: 0;
            }
        </style>

        <style type="text/css">
            body { font-family: Arial;
                   font-size: 10pt;
                   color: #444;}
            
            h1 { border-bottom: 1px dotted #DDD;
                 text-align:left;
                 padding: 15px 0;
                 font-size: 3em; }

            h2 { border-bottom: 1px dotted #DDD;
                 color: #555;
                 font-family: Times New Roman;
                 font-weight: normal;
                 font-size: 2em;
                 padding: 20px;
                 text-align:center; }

            a { color: #777;
                text-decoration: none;
                font-family: Georgia;
                font-style:italic; }
            a:hover { text-decoration: underline; }

            code { font-size: 0.8em;
                   font-family: Courier New;
                   display:block;}

            #page { width: 860px;
                    position:relative;
                    left:50%;
                    margin-left: -430px;
                    z-index: 100000; }
            
            #header { border-bottom: 1px dotted #AAA;
                      margin-bottom: 10px; }
            #content { padding: 0 20px; }
            #footer { text-align: center;
                      font-family:Times New Roman;
                      font-style:italic;
                      border-top: 1px dotted #DDD;
                      padding-top: 8px;
                      margin-top: 25px; }
            #footer,
            #footer a { font-size: 0.8em; }
            #comment { font-family: Times New Roman;
                       font-style: italic;
                       padding: 15px 0 0 4px; }

            p.benchmark-description { border-bottom: 4px solid #eee;
                                      text-align: center;
                                      font-size: 0.8em;
                                      padding: 10px 0; }

            .option { clear:both;
                      border-top: 1px solid #777;
                      background: #FFF; }
            .option.last { border-bottom: 1px solid #777; }
            .option .general-informations {
                      overflow:hidden; }
            .option .general-informations { padding: 10px 0; }
            .option .additional-informations { font-size: 0.7em; 
                                               padding: 5px 30px; }
            .option .code { padding: 5px 10px; }
            .option .code .label { font-family: Times New Roman;
                                   font-weight: bold;
                                   font-style: italic;
                                   color: #ccc;
                                   margin-bottom: 4px; }
            .option .code code { padding-left: 2r5px; }

            .option .additional-informations,
            .option .code { border-top: 1px solid #ccc;
                            clear:both;}

            .option .performance { width: 100px;
                                      float:left;
                                      padding: 3px 3px;
                                      margin: 0 5px;
                                      text-align:center;}
            .option .performance.green  { background: #C3DFC7; }
            .option .performance.yellow { background: #FAFFCF; }
            .option .performance.red    { background: #DFC3C3; }
            .option .performance .multi { font-size: 0.6em;padding:0 2px; }

            .option .performance .label,
            .option .description .label { display:none; }

            .option .description { float:left;
                                   padding: 3px 3px;
                                   line-height: 12pt; }
            .option .description code { border-left: 5px solid #eee;
                                        padding: 2px 0 2px 5px; }
            .option .info { float: right;
                               padding: 3px 3px; }
            .option .info .time,
            .option .info .memory { font-family: Times New Roman;
                                       font-style: italic;
                                       font-size: 0.8em;
                                       color: #aaa;
                                       margin-left: 5px; }
            .option .info a { border-left: 1px solid #ccc;
                                 font-size: 0.75em;
                                 padding-left:10px;
                                 margin:0 5px 0 10px; }
            
            #dialogs { position:relative; }

            .dialog { text-align:center;
                               padding: 10px 0;
                               font-style:italic;
                               font-family:Times New Roman;

                               border-bottom: 1px solid #777;
                               border-top: 1px solid #777; }
            .dialog.warning { background-color: #FAFFCF; }
            .dialog.error   { background-color: #DFC3C3; }
            .dialog.warning p,
            .dialog.error p { background-position: center left;
                                       background-repeat: no-repeat;
                                       padding-left: 25px;
                                       display: inline; }
            .dialog.warning p { background-image:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAIsSURBVDjLpVNLSJQBEP7+h6uu62vLVAJDW1KQTMrINQ1vPQzq1GOpa9EppGOHLh0kCEKL7JBEhVCHihAsESyJiE4FWShGRmauu7KYiv6Pma+DGoFrBQ7MzGFmPr5vmDFIYj1mr1WYfrHPovA9VVOqbC7e/1rS9ZlrAVDYHig5WB0oPtBI0TNrUiC5yhP9jeF4X8NPcWfopoY48XT39PjjXeF0vWkZqOjd7LJYrmGasHPCCJbHwhS9/F8M4s8baid764Xi0Ilfp5voorpJfn2wwx/r3l77TwZUvR+qajXVn8PnvocYfXYH6k2ioOaCpaIdf11ivDcayyiMVudsOYqFb60gARJYHG9DbqQFmSVNjaO3K2NpAeK90ZCqtgcrjkP9aUCXp0moetDFEeRXnYCKXhm+uTW0CkBFu4JlxzZkFlbASz4CQGQVBFeEwZm8geyiMuRVntzsL3oXV+YMkvjRsydC1U+lhwZsWXgHb+oWVAEzIwvzyVlk5igsi7DymmHlHsFQR50rjl+981Jy1Fw6Gu0ObTtnU+cgs28AKgDiy+Awpj5OACBAhZ/qh2HOo6i+NeA73jUAML4/qWux8mt6NjW1w599CS9xb0mSEqQBEDAtwqALUmBaG5FV3oYPnTHMjAwetlWksyByaukxQg2wQ9FlccaK/OXA3/uAEUDp3rNIDQ1ctSk6kHh1/jRFoaL4M4snEMeD73gQx4M4PsT1IZ5AfYH68tZY7zv/ApRMY9mnuVMvAAAAAElFTkSuQmCC); }
            .dialog.error p { background-image:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAJPSURBVDjLpZPLS5RhFMYfv9QJlelTQZwRb2OKlKuINuHGLlBEBEOLxAu46oL0F0QQFdWizUCrWnjBaDHgThCMoiKkhUONTqmjmDp2GZ0UnWbmfc/ztrC+GbM2dXbv4ZzfeQ7vefKMMfifyP89IbevNNCYdkN2kawkCZKfSPZTOGTf6Y/m1uflKlC3LvsNTWArr9BT2LAf+W73dn5jHclIBFZyfYWU3or7T4K7AJmbl/yG7EtX1BQXNTVCYgtgbAEAYHlqYHlrsTEVQWr63RZFuqsfDAcdQPrGRR/JF5nKGm9xUxMyr0YBAEXXHgIANq/3ADQobD2J9fAkNiMTMSFb9z8ambMAQER3JC1XttkYGGZXoyZEGyTHRuBuPgBTUu7VSnUAgAUAWutOV2MjZGkehgYUA6O5A0AlkAyRnotiX3MLlFKduYCqAtuGXpyH0XQmOj+TIURt51OzURTYZdBKV2UBSsOIcRp/TVTT4ewK6idECAihtUKOArWcjq/B8tQ6UkUR31+OYXP4sTOdisivrkMyHodWejlXwcC38Fvs8dY5xaIId89VlJy7ACpCNCFCuOp8+BJ6A631gANQSg1mVmOxxGQYRW2nHMha4B5WA3chsv22T5/B13AIicWZmNZ6cMchTXUe81Okzz54pLi0uQWp+TmkZqMwxsBV74Or3od4OISPr0e3SHa3PX0f3HXKofNH/UIG9pZ5PeUth+CyS2EMkEqs4fPEOBJLsyske48/+xD8oxcAYPzs4QaS7RR2kbLTTOTQieczfzfTv8QPldGvTGoF6/8AAAAASUVORK5CYII=); }

            #dialogs .dialog:last-of-type { display: block; }
            #dialogs .dialog { display:none; }
            #dialogs .dialog { display: expression((this.parentNode.lastChild == this)? "block" : "none" ); }
            #dialogs .dialog .percentage { border: 1px solid #ccc; padding: 4px; margin: 10px 15px 0 15px; height: 12px; }
            #dialogs .dialog .percentage .bar { background: #eee; height:12px; text-align:center;font-size:0.8em; }

            #content .entry:last-of-type { display:block; }
            #content .entry { display:none; }
            #content .entry { display: expression((this.parentNode.lastChild == this)? "block" : "none" ); }
        </style>
    </head>
    <body>
        <div id="page">
            <div id="header">
                <h1>Benchmark</h1>
                <h2><?php echo $this->benchmark_title; ?></h2>
                <?php if ( $this->benchmark_description ) : ?>
                <p class="benchmark-description"><?php echo $this->benchmark_description; ?></p>
                <?php endif; ?>
            </div>
            <div id="content">
<?php if ( $this->benchmark_targets != null && count($this->benchmark_targets) > 0 ) : ?>
                <ul id="dialogs" class="entry"><?php endif; ?>