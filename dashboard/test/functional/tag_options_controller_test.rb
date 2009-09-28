require 'test_helper'

class TagOptionsControllerTest < ActionController::TestCase
  test "should get index" do
    get :index
    assert_response :success
    assert_not_nil assigns(:tag_options)
  end

  test "should get new" do
    get :new
    assert_response :success
  end

  test "should create tag_option" do
    assert_difference('TagOption.count') do
      post :create, :tag_option => { }
    end

    assert_redirected_to tag_option_path(assigns(:tag_option))
  end

  test "should show tag_option" do
    get :show, :id => tag_options(:one).to_param
    assert_response :success
  end

  test "should get edit" do
    get :edit, :id => tag_options(:one).to_param
    assert_response :success
  end

  test "should update tag_option" do
    put :update, :id => tag_options(:one).to_param, :tag_option => { }
    assert_redirected_to tag_option_path(assigns(:tag_option))
  end

  test "should destroy tag_option" do
    assert_difference('TagOption.count', -1) do
      delete :destroy, :id => tag_options(:one).to_param
    end

    assert_redirected_to tag_options_path
  end
end
