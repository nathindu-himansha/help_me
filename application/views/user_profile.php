<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Help Me | User Profile</title>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

  <script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/1.8.2/jquery.min.js" type="text/javascript"></script>
  <script src="http://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.4.2/underscore-min.js" type="text/javascript"></script>
  <script src="http://cdnjs.cloudflare.com/ajax/libs/backbone.js/0.9.2/backbone-min.js"></script>
</head>
<body>

<div id="profile-section">
  <script type="text/template" id="user-profile-template">
    <!-- welcome section -->
    <div class="container">
      <div class="row ">
        <div class="col-12">
          <h3>Welcome <%= data ? data.firstName: '' %></h3>
        </div>
      </div>
    </div>


    <!-- questions section -->
    <div class="container">
      <div class="row ">
        <div class="col-12">
          <h5>Your Questions</h5>

          <% _.each(data.questions, function(question) { %>
            <%= question.question_title %></br>

          <% }); %>
        </div>
      </div>
    </div>

          </br>
    <!-- answers section -->
    <div class="container">
      <div class="row ">
        <div class="col-12">
          <h5>Your Answers</h5>

          <% _.each(data.answers, function(answer) { %>
            <%= answer.answer %></br>

          <% }); %>
        </div>
      </div>
    </div>

    </br>

    <!-- logout section -->
    <div class="container">
        <div class="row ">
      
          <div class="col-12 text-center" >
            <button type="button" class="btn btn-warning">Logout</button>
          </div>
      
        </div>
    </div>

  </script>
</div>


<script lang="Javascript">

  function htmlEncode(value){
    return $('<div/>').text(value).html();
  }

  var UserProfileModel = Backbone.Model.extend({
    url:"http://localhost/help_me/index.php/api/user?id=22"
  });
  var userProfileModel = new UserProfileModel()

  var UserProfileView = Backbone.View.extend({
    el:'#profile-section',
    events:{},
    model:userProfileModel,
    initialize:function(){
      this.retrieveProfile()
    },
    render:function(){
      this.retrieveProfile()
    },
    retrieveProfile:function(){
      var self = this;
      userProfileModel.fetch({
        headers: {'Authorization' :'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyIjp7ImlkIjoiMjIiLCJmTmFtZSI6Ik5hdGhpbmR1IiwiZW1haWwiOiJhYWExMjM0NTY4NzkwQGdtYWlsLmNvbTAxMSJ9LCJpYXQiOjE2NzE5ODMyNjEsImV4cCI6MTY3MTk4Njg2MX0.TcZbi00CRZ-6oEOMTZg2rXwofXb0AImEwps5KBpNDOo'},
        async:false,
        contentType:'application/json',
        success: function(users, response){
          console.log("SUCCESS - userProfileModel-fetch()");
          console.log(response);

          var template = _.template($('#user-profile-template').html(), {data: response.data});
          self.$el.html(template);
        },
        error: function (model, response) {
        console.log("ERROR - userProfileModel fetch() CODE: "+response.status +" STATUS: "+response.statusText);
        }
      });
    }
  });

  var userProfileView = new UserProfileView();
</script>

</body>
</html>
