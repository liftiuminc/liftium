require 'test_helper'

class TagsControllerTest < ActionController::TestCase
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
      get :show, :id => Tag.first
      assert_template 'show'
    end
  end
  
  context "new action" do
    should "render new template" do
      login_as_admin
      get :new
      assert_redirected_to "/tags/select_network"
    end
  end
  
  context "create action" do
    # FIXME remember the network id from select_network
    should "render new template when model is invalid" do
  #    Tag.any_instance.stubs(:valid?).returns(false)
  #    post :create
  #    assert_template 'new'
    end
    
    should "redirect when model is valid" do
  #    Tag.any_instance.stubs(:valid?).returns(true)
  #    post :create
  #    assert_redirected_to tag_url(assigns(:tag))
    end
  end
  
  context "edit action" do
    should "render edit template" do
      login_as_admin
      get :edit, :id => Tag.first
      assert_template 'edit'
    end
  end
  
  context "update action" do
    should "render edit template when model is invalid" do
      login_as_admin
      Tag.any_instance.stubs(:valid?).returns(false)
      put :update, :id => Tag.first
      assert_template 'edit'
    end
  
    should "redirect to list when model is valid" do
      login_as_admin
      Tag.any_instance.stubs(:valid?).returns(true)
      put :update, :id => Tag.first
      assert_redirected_to tags_url
    end
  end
  
  context "destroy action" do
    should "destroy model and redirect to index action" do
      login_as_admin
      tag = Tag.first
      delete :destroy, :id => tag
      assert_redirected_to tags_url
      assert !Tag.exists?(tag.id)
    end
  end
end
