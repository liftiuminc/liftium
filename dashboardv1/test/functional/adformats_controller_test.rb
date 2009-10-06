require 'test_helper'

class AdformatsControllerTest < ActionController::TestCase
  test "should get index" do
    get :index
    assert_response :success
    assert_not_nil assigns(:adformats)
  end

  test "should get new" do
    get :new
    assert_response :success
  end

  test "should create adformat" do
    assert_difference('Adformat.count') do
      post :create, :adformat => { }
    end

    assert_redirected_to adformat_path(assigns(:adformat))
  end

  test "should show adformat" do
    get :show, :id => adformats(:one).to_param
    assert_response :success
  end

  test "should get edit" do
    get :edit, :id => adformats(:one).to_param
    assert_response :success
  end

  test "should update adformat" do
    put :update, :id => adformats(:one).to_param, :adformat => { }
    assert_redirected_to adformat_path(assigns(:adformat))
  end

  test "should destroy adformat" do
    assert_difference('Adformat.count', -1) do
      delete :destroy, :id => adformats(:one).to_param
    end

    assert_redirected_to adformats_path
  end
end
