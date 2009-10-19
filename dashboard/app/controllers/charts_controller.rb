class ChartsController < ApplicationController

  if Rails.configuration.environment != "test"
     before_filter :require_user
  end

  def tag
    @tag = Tag.find(params[:id])
  end

  def misc_stat
  end

end
