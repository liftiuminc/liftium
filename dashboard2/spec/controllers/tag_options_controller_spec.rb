require File.dirname(__FILE__) + '/../spec_helper'
 
describe TagOptionsController do
  fixtures :all
  integrate_views
  
  it "index action should render index template" do
    get :index
    response.should render_template(:index)
  end
  
  it "show action should render show template" do
    get :show, :id => TagOption.first
    response.should render_template(:show)
  end
  
  it "new action should render new template" do
    get :new
    response.should render_template(:new)
  end
  
  it "create action should render new template when model is invalid" do
    TagOption.any_instance.stubs(:valid?).returns(false)
    post :create
    response.should render_template(:new)
  end
  
  it "create action should redirect when model is valid" do
    TagOption.any_instance.stubs(:valid?).returns(true)
    post :create
    response.should redirect_to(tag_option_url(assigns[:tag_option]))
  end
  
  it "edit action should render edit template" do
    get :edit, :id => TagOption.first
    response.should render_template(:edit)
  end
  
  it "update action should render edit template when model is invalid" do
    TagOption.any_instance.stubs(:valid?).returns(false)
    put :update, :id => TagOption.first
    response.should render_template(:edit)
  end
  
  it "update action should redirect when model is valid" do
    TagOption.any_instance.stubs(:valid?).returns(true)
    put :update, :id => TagOption.first
    response.should redirect_to(tag_option_url(assigns[:tag_option]))
  end
  
  it "destroy action should destroy model and redirect to index action" do
    tag_option = TagOption.first
    delete :destroy, :id => tag_option
    response.should redirect_to(tag_options_url)
    TagOption.exists?(tag_option.id).should be_false
  end
end
