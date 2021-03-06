using System;
using System.Data;
using System.Configuration;
using System.Collections;
using System.Web;
using System.Web.Security;
using System.Web.UI;
using System.Web.UI.WebControls;
using System.Web.UI.WebControls.WebParts;
using System.Web.UI.HtmlControls;
using System.Text;
using InfoSoftGlobal;
public partial class MultiSeries : System.Web.UI.Page
{
    protected void Page_Load(object sender, EventArgs e)
    {

    }
    public string GetProductSalesChartHtml()
    {
        //In this example, we plot a multi series chart from data contained
        //in an array. The array will have three columns - first one for data label (product)
        //and the next two for data values. The first data value column would store sales information
        //for current year and the second one for previous year.

        //Let//s store the sales data for 6 products in our array. We also store
        //the name of products. 
        object[,] arrData = new object[6, 3];
        //Store Name of Products
        arrData[0, 0] = "Product A";
        arrData[1, 0] = "Product B";
        arrData[2, 0] = "Product C";
        arrData[3, 0] = "Product D";
        arrData[4, 0] = "Product E";
        arrData[5, 0] = "Product F";
        //Store sales data for current year
        arrData[0, 1] = 567500;
        arrData[1, 1] = 815300;
        arrData[2, 1] = 556800;
        arrData[3, 1] = 734500;
        arrData[4, 1] = 676800;
        arrData[5, 1] = 648500;
        //Store sales data for previous year
        arrData[0, 2] = 547300;
        arrData[1, 2] = 584500;
        arrData[2, 2] = 754000;
        arrData[3, 2] = 456300;
        arrData[4, 2] = 754500;
        arrData[5, 2] = 437600;

        //Now, we need to convert this data into multi-series XML. 
        //We convert using string concatenation.
        //xmlData - Stores the entire XML
        //categories - Stores XML for the <categories> and child <category> elements
        //currentYear - Stores XML for current year's sales
        //previousYear - Stores XML for previous year's sales
        StringBuilder xmlData = new StringBuilder();
        StringBuilder categories=new StringBuilder();
        StringBuilder currentYear= new StringBuilder();
        StringBuilder previousYear= new StringBuilder();

        //Initialize <chart> element
        xmlData.Append("<chart caption='Sales by Product' numberPrefix='$' formatNumberScale='1' rotateValues='1' placeValuesInside='1' decimals='0' >");

        //Initialize <categories> element - necessary to generate a multi-series chart
        categories.Append("<categories>");

        //Initiate <dataset> elements
        currentYear.Append("<dataset seriesName='Current Year'>");
        previousYear.Append("<dataset seriesName='Previous Year'>");

        //Iterate through the data	
        for (int i = 0; i < arrData.GetLength(0); i++)
        {
            //Append <category name='...' /> to strCategories
            categories.AppendFormat("<category name='{0}' />",arrData[i, 0]);
            //Add <set value='...' /> to both the datasets
            currentYear.AppendFormat("<set value='{0}' />",arrData[i, 1]);
            previousYear.AppendFormat("<set value='{0}' />",arrData[i, 2]);
        }

        //Close <categories> element
        categories.Append("</categories>");

        //Close <dataset> elements
        currentYear.Append("</dataset>");
        previousYear.Append("</dataset>");

        //Assemble the entire XML now
        xmlData.Append(categories.ToString());
        xmlData.Append(currentYear.ToString());
        xmlData.Append(previousYear.ToString());
        xmlData.Append("</chart>");

        //Create the chart - MS Column 3D Chart with data contained in xmlData
        return FusionCharts.RenderChart("../FusionCharts/MSColumn3D.swf", "", xmlData.ToString(), "productSales", "600", "300", false, false);
    }
}
