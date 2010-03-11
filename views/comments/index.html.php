<p><%= $this->view->link_to("New comment", "new_comment_url") %></p>

INDEX VIEW for commments.

Listing comments:
<ul>
<% foreach($this->comments as $comment): %>
  <li><%= $comment->content %></li>
<% endforeach; %>
</ul>