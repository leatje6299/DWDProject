<!-- 
A very simple input form View template:
note that the form method is POST, and the action
is the URL for the route that handles form input.
 -->

<p>This is a simple form</p>
<form id="form1" name="form1" method="post" action="<?= ($BASE) ?>/simpleform">
  Enter your name, your third place and the reason why it is:
  <input name="name" type="text" placeholder="Steve" id="name" size="50" />
  <input name="thirdplace" type="text" placeholder="The University" id="location" size="50" />
  <input name="reason" type="text" placeholder="The reason I chose this place is..." id="reason" size="500" />
  <p>
    <input type="submit" name="Submit" value="Submit" />
  </p>
</form>