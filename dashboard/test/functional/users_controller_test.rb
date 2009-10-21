require 'test_helper'

# TODO: Test for new users self-creating accounts

class UsersControllerTest < ActionController::TestCase
  setup :activate_authlogic

  context "index action NOT logged in" do
    setup { get :index }
    should_redirect_to "login url" do
      new_user_session_url
    end
  end

  context "index action" do
    should "render index template" do
      login_as_admin
      get :index
      assert_template 'index'
    end
  end
  
  context "show action" do
    should "render show template" do
      login_as_admin
      get :show, :id => User.first
      assert_template 'show'
    end
  end
  
  context "new action" do
    should "render new template" do
      login_as_admin
      get :new
      assert_template 'new'
    end
  end
  
  context "create action" do
    should "render new template when model is invalid" do
      login_as_admin
      User.any_instance.stubs(:valid?).returns(false)
      post :create
      assert_template 'new'
    end
    
    should "redirect when model is valid" do
      login_as_admin
      User.any_instance.stubs(:valid?).returns(true)
      post :create
      assert_redirected_to user_url(assigns(:user))
    end
  end
  
  context "edit action" do
    should "render edit template" do
      login_as_admin
      get :edit, :id => User.first
      assert_template 'edit'
    end
  end
  
  context "update action" do
    should "render edit template when model is invalid" do
      login_as_admin
      User.any_instance.stubs(:valid?).returns(false)
      put :update, :id => User.first
      assert_template 'edit'
    end
  
    should "redirect to user list when model is valid" do
      login_as_admin
      User.any_instance.stubs(:valid?).returns(true)
      put :update, :id => User.first
      assert_redirected_to users_url
    end
  end
  
  context "destroy action" do
    should "destroy model and redirect to index action" do
      login_as_admin
      user = User.first
      delete :destroy, :id => user
      assert_redirected_to users_url
      assert !User.exists?(user.id)
    end
  end
end
