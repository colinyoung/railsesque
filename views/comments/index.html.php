INDEX VIEW for commments.

Listing comments:
<ul>
<? foreach($this->comments as $comment): ?>
  <li><?=$comment->content;?></li>
<? endforeach; ?>
</ul>