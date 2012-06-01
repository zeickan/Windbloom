$(document).ready(function() {
  
    $('.reply').click(function(e){
        
        var id = $(this).attr('rel');
        
        if( $(this).html() == "Comment" ){
            
            newReply(id);
            
            $(this).html('Close');
        
        } else {
            
            $("#reply-"+id).html(' ');
            
            $(this).html('Comment');
            
        }

    });
    
     $('#sendmsg').click(function() { 
            
            if( $("#textmsg").val() == '' ){
                
                $.blockUI({ message: "<h1>All fields are required</h1>" });                
                
            } else {
                
                $("#formmsg").submit();
            }
            
            setTimeout($.unblockUI, 1000);
            
            return false;
            
    }); 
 
        $('#yes').click(function() { 
            // update the block message 
            $.blockUI({ message: "<h1>Remote call in progress...</h1>" }); 
 
            $.ajax({ 
                url: 'wait.php', 
                cache: false, 
                complete: function() { 
                    // unblock when remote call returns 
                    $.unblockUI(); 
                } 
            }); 
        }); 
 
        $('#no').click(function() { 
            $.unblockUI(); 
            return false; 
        }); 
  
});


function newReply(id){
    
    var html = '<div id="formWrapper" class="replyForm"><form action="'+toreply+'" method="post" enctype="multipart/form-data">';
    
        html+= '<div id="startCommentForm" class="left"><input name="reply" type="hidden" value="'+id+'"><br>';
                
        //html+= '<input name="title" size="30" maxlength="150" placeholder="Message subject">';
                
        html+= '<textarea name="msg" cols="50" rows="5" placeholder="Type your comment"></textarea></div>';
                
        html+= ' <div id="sendCommentwrapper" class="left"><input type="submit" value="Send" class=" submit replySubmit" /></div></form></div>'; 
    
    $("#reply-"+id).html( html );    
    
}