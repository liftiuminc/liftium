require 'test_helper'

class AdFormatsControllerTest < ActionController::TestCase
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
      get :show, :id => AdFormat.first
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
      AdFormat.any_instance.stubs(:valid?).returns(false)
      post :create
      assert_template 'new'
    end
    
    should "redirect when model is valid" do
      login_as_admin
      AdFormat.any_instance.stubs(:valid?).returns(true)
      post :create
      assert_redirected_to ad_format_url(assigns(:ad_format))
    end
  end
  
  context "edit action" do
    should "render edit template" do
      login_as_admin
      get :edit, :id => AdFormat.first
      assert_template 'edit'
    end
  end
  
  context "update action" do
    should "render edit template when model is invalid" do
      login_as_admin
      AdFormat.any_instance.stubs(:valid?).returns(false)
      put :update, :id => AdFormat.first
      assert_template 'edit'
    end
  
    should "redirect when model is valid" do
      login_as_admin
      AdFormat.any_instance.stubs(:valid?).returns(true)
      put :update, :id => AdFormat.first
      assert_redirected_to ad_formats_url
    end
  end
  
  context "destroy action" do
    should "destroy model and redirect to index action" do
      login_as_admin
      ad_format = AdFormat.first
      delete :destroy, :id => ad_format
      assert_redirected_to ad_formats_url
      assert !AdFormat.exists?(ad_format.id)
    end
  end
end
