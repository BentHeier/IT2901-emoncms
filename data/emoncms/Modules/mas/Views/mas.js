
var mas = {

  'list':function()
  {
    var result = {};
    $.ajax({ url: path+"mas/list.json?json={%27info%27:true}", dataType: 'json', async: false, 
		success: function(data) {
			
			result = data['tasks'];
		
			
			
		
		}
			});
    return result;
  },

  'set':function(id, fields)
  {
    var result = {};
    $.ajax({ url: path+"mas/set.json", data: "id="+id+"&json="+JSON.stringify(fields), async: false, success: function(data){} });
    return result;
  },

  'remove':function(id)
  {
    $.ajax({ url: path+"mas/delete.json", data: "id="+id, async: false, success: function(data){} });
  },

  // Process

  'add_process':function(inputid,processid,arg,newfeedname)
  {
    var result = {};
    $.ajax({ url: path+"drive/process/add.json", data: "inputid="+inputid+"&processid="+processid+"&arg="+arg+"&newfeedname="+newfeedname, async: false, success: function(data){result = data;} });
    return result;
  },

  'get_parameters':function(driverid)
  {
    var result = {};
    $.ajax({ url: path+"driver/parameters.json", data: "driverid="+driverid, async: false, dataType: 'json', success: function(data){result = data;} });
    return result;
  },

  'delete_process':function(inputid,processid)
  {
    var result = {};
    $.ajax({ url: path+"driver/mas/delete.json", data: "inputid="+inputid+"&processid="+processid, async: false, success: function(data){result = data;} });
    return result;
  },

  'move_process':function(inputid,processid,moveby)
  {
    var result = {};
    $.ajax({ url: path+"driver/process/move.json", data: "inputid="+inputid+"&processid="+processid+"&moveby="+moveby, async: false, success: function(data){result = data;} });
    return result;
  },

  'stop':function(alg)
  {
	 var result = {};
     $.ajax({ url: path+"mas/stop.json", data: "agent="+alg, async: false, success: function(data){result = data;} }); 
     console.log(result);
     return result;
  },
  'status':function()
  {
	 var result = {};
     $.ajax({ url: path+"mas/status.json", data: "", async: false, success: function(data){result = data;} }); 

     return result;
  },
  'start':function(alg)
  {
     var result = {};
     $.ajax({ url: path+"mas/start.json", data: "agent="+alg, async: false, success: function(data){result = data;} }); 
    
     return  result;
     
  },
  'settings':function()
  {
    
     var result = {};
    $.ajax({ url: path+"mas/settings.json", data: "", async: false, success: function(data){
		
		
		result = data;} });
    
     if(result.length==0)
     {
		 $.ajax({ url: path+"mas/savesettings.json", data: "id=0&json={\"id\":0, \"name\": \"dropboxdir\", \"value\": \"Not Set\"}", async: false, success: function(data){result = data;} });
		 $.ajax({ url: path+"mas/savesettings.json", data: "id=1&json={\"id\":0, \"name\": \"masdir\", \"value\": \"Not Set\"}", async: false, success: function(data){result = data;} });
     
		 
       return  [{"id":0, "name": "dropboxdir", "value": "Not Set"}, {"id":1, "name": "masdir", "value": "Not Set"}];
	}
	else 
	   return result;
     
  },
  'savesettings':function(id,fields)
  {
    
    var result = {};
    var i=parseInt(id);
    switch(i)
    {
     case 0: fields.name="dropboxdir"; console.log("ciao");break;
     case 1: fields.name="masdir"; break;
	}
	
     $.ajax({ url: path+"mas/savesettings.json", data: "id="+id+"&json="+JSON.stringify(fields), async: false, success: function(data){result = data;} });
     return result;
     
  }  ,
  'profiles':function()
  {
    var result = {};
     $.ajax({ url: path+"mas/profiles.json", async: false, success: function(data){result = data;
		
		 } });
     return result;
     
  }
  ,
  'tsprofile':function(name)
  {
    var result = {};
     $.ajax({ url: path+"mas/tsprofile.json", data: "name="+name, async: false, success: function(data){result = data;
	
		 } });
     return result;
     
  },
  'profileinfo':function(name)
  {
    var result = {};
     $.ajax({ url: path+"mas/profileinfo.json", data: "name="+name, async: false, success: function(data){result = data;
	
		 } });
     return result;
     
  }

}

