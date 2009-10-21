class ChartsController < ApplicationController

  before_filter :require_user

  def tag
    @tag = Tag.find(params[:id])
  end

  def misc_stat
  end

end
