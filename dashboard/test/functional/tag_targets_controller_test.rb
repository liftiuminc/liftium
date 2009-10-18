require 'test_helper'

class TagTargetsControllerTest < ActionController::TestCase
  context "index action" do
    should "render index template" do
      get :index
      assert_template 'index'
    end
  end
  
  context "show action" do
    should "render show template" do
      get :show, :id => TagTarget.first
      assert_template 'show'
    end
  end
  
  context "new action" do
    should "render new template" do
      get :new
      assert_template 'new'
    end
  end
  
  context "create action" do
    should "render new template when model is invalid" do
      TagTarget.any_instance.stubs(:valid?).returns(false)
      post :create
      assert_template 'new'
    end
    
    should "redirect when model is valid" do
      TagTarget.any_instance.stubs(:valid?).returns(true)
      post :create
      assert_redirected_to tag_target_url(assigns(:tag_target))
    end
  end
  
  context "edit action" do
    should "render edit template" do
      get :edit, :id => TagTarget.first
      assert_template 'edit'
    end
  end
  
  context "update action" do
    should "render edit template when model is invalid" do
      TagTarget.any_instance.stubs(:valid?).returns(false)
      put :update, :id => TagTarget.first
      assert_template 'edit'
    end
  
    should "redirect when model is valid" do
      TagTarget.any_instance.stubs(:valid?).returns(true)
      put :update, :id => TagTarget.first
      assert_redirected_to tag_target_url(assigns(:tag_target))
    end
  end
  
  context "destroy action" do
    should "destroy model and redirect to index action" do
      tag_target = TagTarget.first
      delete :destroy, :id => tag_target
      assert_redirected_to tag_targets_url
      assert !TagTarget.exists?(tag_target.id)
    end
  end
end
