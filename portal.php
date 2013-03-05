<?php
    //check for login
    session_start();
   // session_write_close();
    if(!isset($_SESSION['valid']) || !$_SESSION['valid']){
        header('Location: index.php?e=0');
        die();
    }

    include "include/common.php";
    
    initializePage();
     session_write_close();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>CForce</title>
        <script src="include/jQueryCookie/jquery.cookie.js" type="text/javascript"></script>
        <script type="text/javascript">
            $(document).ready(function(){
                //selectors
                var tabs = $("#tabs");
                var tab_li = $("#tabs li:not(.disabled_tab)");
                var feedback_ti = $("#feedback_ti");
                
                //initialze tabs
                tabs.tabs({ 
                    cookie: { expires: 30 },
                    cache: false
                });
                tab_li.css("width", 1000/tab_li.length);
                
                //loading icon
                $("#tabs .ui-tabs-panel:not(#tabs-processing)").append("<img src='images/cms_loading.gif' class='tab_spinner'/>");
                
                //hash navigation
                var hash = "";
                if(location.hash == ""){
                    location.hash = tabs.tabs("option" , "active");
                }
                else{
                    hash = location.hash;
                    evalHash(true); 
                }
                
                
                //listen for hash change (for tab navigation)
                $(window).bind('hashchange', function() {
                    evalHash(false);
                });
                
                function evalHash(initialize){
                    if(hash != ""){
                        var hashArr = location.hash.split("#");
                        var hash_tab = parseFloat(hashArr[1]);
                        if(hash.substr(0,2) != location.hash.substr(0,2) || initialize){
                            if(initialize){
                               // alert("initialize new:" + location.hash + " old:" + hash);
                            }
                            else{
                                //alert("tab change new:" + location.hash + " old:" + hash);
                            }
                            //select tab
                            tabs.tabs("select", hash_tab);
                        }
                        else{
                            if(hash.length > location.hash.length){
                               // alert("up detail new:" + location.hash + " old:" + hash);
                            }
                            else{
                                //alert("down detail new:" + location.hash + " old:" + hash);
                            }
                            tabs.tabs("load", hash_tab);
                        }
                    }
                    hash = location.hash;
                }
                
                //help modal
                $("#help_modal").dialog({
                    autoOpen: false,
                    height: 'auto',
                    width: 800,
                    modal: true,
                    buttons: {
                        Close: function() {
                            $(this).dialog("close");
                        }
                    }
                });
                
                //listen for help modal clicks
                $("#help_modal_btn2").click(function(){
                    $("#help_modal").dialog( "open" );
                    return false;
                });
                
                //clear feedback default text on click
                feedback_ti.click(function(){
                    if(feedback_ti.hasClass("default")){
                        feedback_ti.val("")
                            .removeClass("default");
                    }
                    return false;
                });
                feedback_ti.blur(function(){
                    if(feedback_ti.val() == ""){
                        feedback_ti.addClass("default")
                            .val("Enter your feedback here...");
                    }
                    return false;
                });
               
                //initialize feedback submit button
                $("#submit_feedback").button()
                    .css({
                        padding:"0px",
                        marginTop: "10px",
                        textMozBorderRadius:"0px",
                        borderRadius:"0px"
                    })
                    .click(function(){
                        var feedback = feedback_ti.val();
                        if(feedback != "" && feedback != "Enter your feedback here..."){
                            $.ajax({
                                url: "include/submit_feedback.php",
                                type: "GET",
                                data: {
                                    feedback : feedback,
                                    active_tab: tabs.tabs("option", "active")
                                },
                                success: function(data) {
                                    feedback_ti.addClass("default")
                                        .val("Enter your feedback here...");
                                    $("#feedback_message").text(data);
                                }
                            });
                        }
                        return false;
                    })
                    .addClass("button2");
                
                //remove title tooltips
                $('[title]').each(function() {
                    $.data(this, 'title', $(this).attr('title'));
                    $(this).removeAttr('title');
                });
                
                //show content
                $("#wrapper").css("display", "block");
                
                $('#to_top').click(function() {
                    $("body").scrollTop(0);
                    return false;
                });
            });
            
            //handle tab clicks
            function go(tab){
                //alert("GO tab:" + tab + " substring of hash:" + location.hash.substring(1,2));
                if(location.hash.substring(1,2) != tab){
                    location.hash = tab
                }
            }
        </script>
    </head>
    <body>
        <div id="wrapper" class="with-footer">
            <div id="header">
                 <div id="header_container" cellpadding="0" cellspacing="0">
                     <div id="logo"></div>
                     <p>Welcome, <?php echo $_SESSION['name']; ?></p>
                     <a id="help_modal_btn2" href="#">Help</a>
                     <a id="logout_btn" href="index.php?lo=1">Logout</a>
                 </div>
            </div> 
            <div id="container">
                <div id="bar1"></div>
                <div id="bar2"></div>
                <div id="tabs">
                    <ul>
                        <li style="z-index:8">
                            <a id="tab0" class="tab_title" title="0" href="modules/home/home.php" onclick="go(0)"><p>Home</p></a>
                        </li>
    <?php if((int)$_SESSION['department'] >= 10): ?>
                        <li style="z-index:7">
                            <a id="tab1" class="tab_title" title="1" href="modules/agents/agents.php" onclick="go(1)"><p>Agents</p></a>
                        </li>
    <?php else : ?>
                        <li class="disabled_tab">
                            <a id="tab1" class="tab_title" title="1" href="" onclick="" style="display: none"><p>Agents</p></a>
                        </li>
    <?php endif; ?>
                        <li style="z-index:6">
                            <a id="tab2" class="tab_title" title="2" href="modules/merchants/merchants.php" onclick="go(2)"><p>Merchants</p></a>
                        </li>
    <?php if((int)$_SESSION['department'] == 10): ?>
                        <li style="z-index:5">
                            <a id="tab3" class="tab_title" title="3" href="modules/contacts/contacts.php" onclick="go(3)"><p>Contacts</p></a>
                        </li>
    <?php else : ?>
                        <li class="disabled_tab">
                            <a id="tab3" class="tab_title" title="3" href="" onclick="" style="display: none"><p>Contacts</p></a>
                        </li>
    <?php endif; ?>
                        <li style="z-index:4">
                            <a id="tab4" class="tab_title" title="4" href="modules/processing/processing.php" onclick="go(4)"><p>Processing</p></a>
                        </li>
                        <li style="z-index:3">
                            <a id="tab5" class="tab_title" title="5" href="modules/documents/documents.php" onclick="go(5)"><p>Documents</p></a>
                        </li>
                        <li style="z-index:2">
                            <a id="tab6" class="tab_title" title="6" href="modules/tools/tools.php" onclick="go(6)"><p>Tools</p></a>
                        </li>
      <?php if((int)$_SESSION['department'] == 10): ?>
                         <li style="z-index:1">
                            <a id="tab7" class="tab_title" title="7" href="modules/residuals/residuals.php" onclick="go(7)"><p>Residuals</p></a>
                        </li>  
                            <?php endif; ?>
                    </ul>
                </div>

                <div id="feedback">
                  <h3>FEEDBACK</h3>
                  <p id="feedback_subtitle">Since this is a Beta version, we would love to hear your suggestions to make C Force better:</p>
                  <textarea id="feedback_ti" cols="40" rows="5" class="default">Enter your feedback here...</textarea>
                  <p id="feedback_message" class="error"></p>
                  <button id="submit_feedback">Submit</button>  
                </div>
            </div>
            <div class="push with-footer"></div>
            <div id="background_tl" class="background_img_top"></div>
            <div id="background_tr" class="background_img_top"></div>
        </div>
        <div id="footer">
            <a id="cms_logo" href="http://www.cmsonline.com">
                <img src="images/CMSLogo_white.png"></img>
            </a>
            <div id="contact_info" >
                <div>
                    <span class="contact_2">Forgot Password? Need Help? Please call us!</span>
                    <span class="contact_1">CUSTOMER SERVICE</span>
                    <p class="contact_3">801.623.4000 / 877.267.4324</p>
                    <p class="contact_4">customerservice@cmsonline.com</p>
                </div>
            </div>
            <div id="copywrite">
                <p class="copywrite_1">&#169; 2013 Complete Merchant Solutions LLC, All Rights Reserved</p>
                <p class="copywrite_2">Complete Merchant Solutions is a Registered ISO and MSP of HSBC Bank USA, National Association,</p>
                <p class="copywrite_2">Buffalo, NY and NATIONAL BANK of California, Los Angeles, CA. American Express requires separate approval.</p>
                <p class="copywrite_2">All trademarks, service marks, and trade names that are referenced in this material are the property of their respective owners.</p>
            </div>
        </div>
        <!--<div id="background_bl" class="background_img_bottom"></div>
        <div id="background_br" class="background_img_bottom"></div>-->
        <div id="bar3"></div>
        <div id="bar4"></div>
        <div id='help_modal'></div>
    </body>
</html>