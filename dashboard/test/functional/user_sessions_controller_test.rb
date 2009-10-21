require 'test_helper'

class UserSessionsControllerTest < ActionController::TestCase
  setup :activate_authlogic

  context "new action" do
    should "render new template" do
      get :new
      assert_template 'new'
    end
  end

  context "publisher_login" do
    setup { post :create, :email => 'unit_test_publisher@liftium.com', :password => 'password' }
    should "render publisher_home" do
       # assert_redirect admin_home_url
       respond_with :success
    end 
  end

  context "admin_login" do
    should "render admin_home" do
       post :create, :email => 'unit_test_publisher@liftium.com', :password => 'liftium1nc'
       # assert_redirect publisher_home_url
       respond_with :success
    end
  end

#  context "logout" do
#    should "when logged in as admin" do
#      login_as_admin
#    end

  #  setup { get publisher_home_url }
  #  should_redirect_to "login url" do
  #    new_user_session_url
  #  end

#  end

end
