class ChartsController < ApplicationController

  def tag
    @tag = Tag.find(params[:id])
  end

  def misc_stat
  end

end
