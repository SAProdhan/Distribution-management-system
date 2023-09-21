## API Documentation

 <summary><b><code>RESOURCE</code>  <code>{base_url}/api/category</code></b></summary>

 ###### Request body (Only for POST/PUT):

```javascript
{
  "name" : string
}
```
##
 <summary><b><code>RESOURCE</code>  <code>{base_url}/api/brand</code></b></summary>

 ##### Request body (Only for POST/PUT):

```javascript
{
  "name" : string
}
```
##
 <summary><b><code>RESOURCE</code>  <code>{base_url}/api/unit</code></b></summary>

 ##### Request body (Only for POST/PUT):

```javascript
{
  "name" : string
}
```
##
 <summary><b><code>RESOURCE</code>  <code>{base_url}/api/department</code></b></summary>

 ##### Request body (Only for POST/PUT):

```javascript
{
  "name" : string
}
```
##
 <summary><b><code>RESOURCE</code>  <code>{base_url}/api/product</code></b></summary>

 ##### Request body (Only for POST/PUT):

```javascript
{
	"name": String,
	"sku": Integer,
	"brand_id": Integer,
	"category_id": Integer,
	"usp": String,
	"price": Float,
	"qty": Integer,
	"description": Text,
}
```
##
 <summary><b><code>POST</code>  <code>{base_url}/api/stock-challan</code></b></summary>

 ##### Request body:

```javascript
{
	"department_id":Integer,
	"challan_no":String,
	"product_data":Array[{
		"id":Integer,
		"qty":Integer
	}]
}
```

##
 <summary><b><code>POST</code>  <code>{base_url}/api/sales</code></b></summary>

 ##### Request body:

```javascript
{
	"paid_amount":Float,
	"products":Array[{
		"id":Integer,
		"qty":Integer,
		"unit":String
	}]
}
```

##
 ##### Response (for all GET request list):

```javascript
{
	"currentPage": Integer,
	"perPage": Integer,
	"total": Integer,
	"data": Array
}
```

