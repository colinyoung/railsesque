<p><a href="/comments">Back to listing all comments</a></p>


<h3>New Comment</h3>
<%= $this->view->startForm($this->comment); %>
<% foreach($this->view->buildForm($this->comment) as $f): %>

    <%= $f->label("email", true); %>
    <%= $f->text_field("email"); %>
    
    <%= $f->label("content", true); %>
    <%= $f->text_area("content"); %>
    
    <%= $f->submit(); %>
<% endforeach; %>
<%= $this->view->endForm(); %>