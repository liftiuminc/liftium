class ChartsController < ApplicationController

  def tag
    @tag = Tag.find(params[:id])
  end

end
