
    <?php require_once('header.php') ;?>    
    <div class="search">
    <form action="search.php" method="get" name="search_form">
      <input type="text"  onkeyup="getUsersForMessages(this.value)" name="q" placeholder="Enter friend name" autocomplete = "off" id="search_text_input">
    </form>
    <div class="search_results_for_messages"></div>
    <div class="search_results_footer_empty"></div>
  </div>
