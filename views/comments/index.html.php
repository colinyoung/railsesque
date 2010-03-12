<p><%= $this->view->link_to("New comment", "new_comment_url") %></p>
<ul>
<% foreach($this->comments as $comment): %>
  <li><%= $comment->email %> wrote: 
    <ul>
      <li><%= $comment->content %></li>
      <li><%= $this->view->link_to("Delete", "delete_comment_url", $comment->id) %></li>      
    </ul>
  </li>
<% endforeach; %>
</ul>